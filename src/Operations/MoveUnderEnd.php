<?php

namespace Echo511\TreeTraversal\Operations;

class MoveUnderEnd extends MoveBase
{

    protected function getMoveDirection() {
        $head = $this->head;
        $target = $this->target;
        if ($head['rgt'] < $target['rgt']) {
            return self::MOVE_DIRECTION_RIGHT;
        }
        return self::MOVE_DIRECTION_LEFT;
    }

    protected function getShiftingIndexesLimits() {
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

    protected function getDepthModifier() {
        return $this->target['dpt'] - $this->head['dpt'] + 1;
    }

}
