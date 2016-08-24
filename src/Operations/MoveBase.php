<?php

namespace Echo511\TreeTraversal\Operations;

use Echo511\TreeTraversal\InvalidMoveExpcetion;
use Echo511\TreeTraversal\Tree;
use FluentLiteral;

/**
 * Base class for moving operations.
 * 
 * The nodes we want to move are considered moving.
 * The nodes that have to move because of the moving nodes are considered shifting.
 */
abstract class MoveBase extends Base
{

    const MOVE_DIRECTION_LEFT = 1;
    const MOVE_DIRECTION_RIGHT = -1;

    /**
     * Indexed and depth of head node.
     * @var array
     */
    protected $head = [];

    /**
     * Indexed and depth of target node.
     * @var array
     */
    protected $target = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * MoveUnderEnd constructor.
     * @param array $head
     * @param array $target
     * @param array $config
     * @param Tree $tree
     */
    public function __construct(array $head, array $target, array $config, Tree $tree)
    {
        parent::__construct($target, $config, $tree);
        $this->head = $head;
        $this->target = $target;
        $this->config = $config;
        $this->tree = $tree;
    }

    /**
     * Return direction of movement.
     *
     * Values can be used as mathematical coefficients.
     *
     * @return int
     */
    abstract protected function getMoveDirection();

    /**
     * Range of shifting indexes.
     * @return array
     */
    abstract protected function getShiftingIndexesLimits();

    /**
     * How should change the depth of moving nodes.
     */
    abstract protected function getDepthModifier();

    /**
     * New parent of the head node after the head node has been moved.
     * @return mixed ID of new parent.
     */
    abstract protected function getHeadNodeNewParent();

    /**
     * Run the logic.
     */
    protected function doRun()
    {
        $config = $this->config;
        if ($this->target['id'] != $this->head['id']) {
            $this->canMove();

            $movingNodes = $this->getMovingNodes();
            $movingNodesRange = $this->getMovingNodesRange();
            $shiftingNodes = $this->getShiftingNodes();
            $shiftingNodesRange = $this->getShiftingNodesRange();

            // update moving nodes
            $this->updateIndexes($movingNodes, self::INDEX_LFT, $this->head['lft'], $this->head['rgt'], $shiftingNodesRange * -1 * $this->getMoveDirection());
            $this->updateIndexes($movingNodes, self::INDEX_RGT, $this->head['lft'], $this->head['rgt'], $shiftingNodesRange * -1 * $this->getMoveDirection());
            $this->updateDepths($movingNodes, $this->getDepthModifier());
            $this->updateNodeParent($this->head['id'], $this->getHeadNodeNewParent());

            // update shifting nodes
            $limits = $this->getShiftingIndexesLimits();
            $this->updateIndexes($shiftingNodes, self::INDEX_LFT, $limits['min'], $limits['max'], $movingNodesRange * $this->getMoveDirection());
            $this->updateIndexes($shiftingNodes, self::INDEX_RGT, $limits['min'], $limits['max'], $movingNodesRange * $this->getMoveDirection());
        }
    }

    /**
     * Throw exception if move cannot be performed.
     * @throws InvalidMoveExpcetion
     */
    protected function canMove()
    {
        if ($this->tree->isChildOf($this->target, $this->head)) {
            throw new InvalidMoveExpcetion("Cannot move parent node under child node.");
        }
    }

    /**
     * Get moving nodes IDs.
     * @return array
     */
    protected function getMovingNodes()
    {
        $head = $this->head;
        return $this->getNodesBetween($head['lft'], $head['rgt']);
    }

    /**
     * Get the distance between indexes of moving nodes.
     * @return int
     */
    protected function getMovingNodesRange()
    {
        return $this->head['rgt'] - $this->head['lft'] + 1;
    }

}
