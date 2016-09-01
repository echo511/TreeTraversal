<?php

namespace Echo511\TreeTraversal\Operations;

class InsertUnderEnd extends InsertBase
{

    protected function doRun()
    {
        if (!$this->target) {
            $lastRoot = $this->tree->getLastRootNode();
            if(!$lastRoot) {
                $this->target = [
                    'id' => null,
                    'rgt' => 1,
                    'dpt' => - 1,
                ];
            } else {
                $this->target = [
                    'id' => null,
                    'rgt' => $lastRoot['rgt'] + 1,
                    'dpt' => - 1,
                ];
            }
        }

        $this->updateIndexes([], self::INDEX_LFT, $this->target['rgt'], null, 2);
        $this->updateIndexes([], self::INDEX_RGT, $this->target['rgt'], null, 2);
        return $this->insertSingleNode($this->target['rgt'], $this->target['dpt'] + 1, $this->target['id']);
    }

}
