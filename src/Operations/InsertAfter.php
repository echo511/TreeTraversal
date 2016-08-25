<?php

namespace Echo511\TreeTraversal\Operations;

class InsertAfter extends InsertBase
{

    protected function doRun()
    {
        if (!$this->target) {
            $lastRoot = $this->tree->getLastRootNode();
            if (!$lastRoot) {
                $this->target = [
                    'id' => null,
                    'rgt' => 0,
                    'dpt' => 0,
                    'prt' => null,
                ];
            } else {
                $this->target = [
                    'id' => null,
                    'rgt' => $lastRoot['rgt'],
                    'dpt' => 0,
                    'prt' => null,
                ];
            }
        }

        $this->updateIndexes([], self::INDEX_LFT, $this->target['rgt'] + 1, null, 2);
        $this->updateIndexes([], self::INDEX_RGT, $this->target['rgt'] + 1, null, 2);
        $this->insertSingleNode($this->target['rgt'] + 1, $this->target['dpt'], $this->target['prt']);
    }

}
