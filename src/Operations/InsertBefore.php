<?php

namespace Echo511\TreeTraversal\Operations;

class InsertBefore extends InsertBase
{

    protected function doRun()
    {
        if (!$this->target) {
            $firstRoot = $this->tree->getFirstRootNode();
            if (!$firstRoot) {
                $this->target = [
                    'id' => null,
                    'lft' => 1,
                    'dpt' => 0,
                    'prt' => null,
                ];
            } else {
                $this->target = [
                    'id' => null,
                    'lft' => $firstRoot['lft'],
                    'dpt' => 0,
                    'prt' => null,
                ];
            }
        }

        $this->updateIndexes([], self::INDEX_LFT, $this->target['lft'], null, 2);
        $this->updateIndexes([], self::INDEX_RGT, $this->target['lft'], null, 2);
        return $this->insertSingleNode($this->target['lft'], $this->target['dpt'], $this->target['prt']);
    }

}
