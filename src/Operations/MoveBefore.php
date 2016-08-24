<?php

namespace Echo511\TreeTraversal\Operations;

class MoveBefore extends MoveBase
{

    protected function getMoveDirection()
    {
        $head = $this->head;
        $target = $this->target;
        if ($head['lft'] < $target['lft']) {
            return self::MOVE_DIRECTION_RIGHT;
        }
        return self::MOVE_DIRECTION_LEFT;
    }

    protected function getShiftingIndexesLimits()
    {
        if ($this->getMoveDirection() == self::MOVE_DIRECTION_LEFT) {
            return [
                'min' => $this->target['lft'],
                'max' => $this->head['lft'] - 1,
            ];
        } else {
            return [
                'min' => $this->head['rgt'] + 1,
                'max' => $this->target['lft'] - 1,
            ];
        }
    }

    protected function getDepthModifier()
    {
        return $this->target['dpt'] - $this->head['dpt'];
    }

    protected function getHeadNodeNewParent()
    {
        return $this->target['prt'];
    }

}
