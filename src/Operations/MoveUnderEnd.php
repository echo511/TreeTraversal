<?php

namespace Echo511\TreeTraversal\Operations;

/**
 * Class MoveUnderEnd
 * @package Echo511\TreeTraversal\Operations
 */
class MoveUnderEnd extends MoveBase
{

	/**
	 * @var \Echo511\TreeTraversal\Tree
	 */
	private $tree;

	public function __construct(array $head, array $target, array $config, \PDO $pdo, \Echo511\TreeTraversal\Tree $tree)
	{
		parent::__construct($head, $target, $config, $pdo);
		$this->tree = $tree;
	}

	protected function canMove()
	{
		if ($this->tree->isChildOf($this->target, $this->head)) {
			throw new \Echo511\TreeTraversal\InvalidMoveExpcetion("Cannot move parent node under child node.");
		}
	}

	/**
	 * Return direction of movement.
	 *
	 * Values can be used as mathematical coefficients.
	 *
	 * @return int
	 */
	protected function getMoveDirection()
	{
		$head = $this->head;
		$target = $this->target;
		if ($head['rgt'] < $target['rgt']) {
			return self::MOVE_DIRECTION_RIGHT;
		}
		return self::MOVE_DIRECTION_LEFT;
	}

	protected function getShiftingIndexesLimits()
	{
		if ($this->getMoveDirection() == self::MOVE_DIRECTION_LEFT) {
			return [
				'min' => $this->target['rgt'],
				'max' => $this->head['lft'] - 1, // except head lft
			];
		} else {
			return [
				'min' => $this->head['rgt'] + 1,
				'max' => $this->target['rgt'] - 1,
			]; // except head rgt and target rgt
		}
	}

	protected function getDepthModifier()
	{
		return $this->target['dpt'] - $this->head['dpt'] + 1;
	}

}
