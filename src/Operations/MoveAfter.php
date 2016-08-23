<?php

namespace Echo511\TreeTraversal\Operations;

/**
 * Class MoveUnderEnd
 * @package Echo511\TreeTraversal\Operations
 */
class MoveAfter extends MoveBase
{

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
				'min' => $this->target['rgt'] + 1,
				'max' => $this->head['lft'] - 1,
			];
		} else {
			return [
				'min' => $this->head['rgt'] + 1,
				'max' => $this->target['rgt'],
			];
		}
	}
	
	protected function getDepthModifier()
	{
		return $this->target['dpt'] - $this->head['dpt'];
	}

}
