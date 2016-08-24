<?php

namespace Echo511\TreeTraversal\Operations;

class InsertAfter extends InsertBase
{

    protected function doRun()
    {
        $this->updateIndexes([], self::INDEX_LFT, $this->target['rgt'] + 1, null, 2);
        $this->updateIndexes([], self::INDEX_RGT, $this->target['rgt'] + 1, null, 2);
        $this->insertSingleNode($this->target['rgt'] + 1, $this->target['dpt'], $this->target['prt']);
    }

}
