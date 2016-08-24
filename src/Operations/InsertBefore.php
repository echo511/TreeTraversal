<?php

namespace Echo511\TreeTraversal\Operations;

class InsertBefore extends InsertBase
{

    protected function doRun()
    {
        $this->updateIndexes([], self::INDEX_LFT, $this->target['lft'], null, 2);
        $this->updateIndexes([], self::INDEX_RGT, $this->target['lft'], null, 2);
        $this->insertSingleNode($this->target['lft'], $this->target['dpt']);
    }

}
