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
abstract class MoveBase
{

    const INDEX_LFT = 'lft';
    const INDEX_RGT = 'rgt';

    /*
     * Direction constants are used as math coefficients.
     */
    const MOVE_DIRECTION_LEFT = 1; // because shifting nodes indexes increase values => hence 1 => DO NOT CHANGE!
    const MOVE_DIRECTION_RIGHT = -1; // because shifting nodes indexes decrease values => hence -1 => DO NOT CHANGE!

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
    private $tree;

    /**
     * MoveUnderEnd constructor.
     * @param array $head
     * @param array $target
     * @param array $config
     * @param Tree $tree
     */
    public function __construct(array $head, array $target, array $config, Tree $tree) {
        $this->head = $head;
        $this->target = $target;
        $this->config = $config;
        $this->tree = $tree;
    }

    /**
     * Run the operation in transaction.
     */
    public function run() {
        $this->tree->getFluent()->getPdo()->beginTransaction();
        $this->doRun();
        $this->tree->getFluent()->getPdo()->commit();
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
     * Run the logic.
     */
    protected function doRun() {
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
    protected function canMove() {
        if ($this->tree->isChildOf($this->target, $this->head)) {
            throw new InvalidMoveExpcetion("Cannot move parent node under child node.");
        }
    }

    /**
     * Get moving nodes IDs.
     * @return array
     */
    protected function getMovingNodes() {
        $head = $this->head;
        return $this->getNodesBetween($head['lft'], $head['rgt']);
    }

    /**
     * Get the distance between indexes of moving nodes.
     * @return int
     */
    protected function getMovingNodesRange() {
        return $this->head['rgt'] - $this->head['lft'] + 1;
    }

    /**
     * IDs of shifting nodes.
     * @return mixed
     */
    protected function getShiftingNodes() {
        $limits = $this->getShiftingIndexesLimits();
        return $this->getNodesBetween($limits['min'], $limits['max']);
    }

    /**
     * Get the distance between indexes of shifting nodes.
     * @return type
     */
    protected function getShiftingNodesRange() {
        $limits = $this->getShiftingIndexesLimits();
        return $limits['max'] - $limits['min'] + 1;
    }

    /**
     * Get IDs of nodes between indexes including parent.
     * @param $min
     * @param $max
     * @return array
     */
    protected function getNodesBetween($min, $max) {
        $config = $this->config;
        $query = $this->tree->table()
                ->select(NULL)// fetch only id
                ->select("$config[id] AS id")
                ->where("($config[lft] >= :min AND $config[lft] <= :max) OR ($config[rgt] >= :min AND $config[rgt] <= :max)", [
                    ':min' => $min,
                    ':max' => $max,
                ])
                ->fetchAll();
        $ids = array_map(function ($key) {
            return $key['id'];
        }, $query);
        return $ids;
    }

    /**
     * 
     * @param array $nodes
     * @param string $index self::INDEX_LFT | self::INDEX_RGT
     * @param type $lftLimit
     * @param type $rgtLimit
     * @param type $value
     */
    protected function updateIndexes($nodes, $index, $lftLimit, $rgtLimit, $value) {
        if (count($nodes) > 0) {
            $config = $this->config;
            $this->tree->getFluent()
                    ->update($config['table'])
                    ->set($config[$index], new FluentLiteral("$config[$index] + $value"))
                    ->where($config['id'], $nodes)
                    ->where("$config[$index] >= :lftLimit AND $config[$index] <= :rgtLimit", [
                        ':lftLimit' => $lftLimit,
                        ':rgtLimit' => $rgtLimit
                    ])
                    ->execute();
        }
    }

    /**
     * Update nodes depths.
     * @param $nodes
     * @param $value
     */
    protected function updateDepths($nodes, $value) {
        $config = $this->config;
        $this->tree->getFluent()
                ->update($config['table'])
                ->set($config['dpt'], new FluentLiteral("$config[dpt] + $value"))
                ->where($config['id'], $nodes)
                ->execute();
    }

}
