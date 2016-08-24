<?php

namespace Echo511\TreeTraversal\Operations;

class InsertUnderEnd extends InsertBase
{

    protected function doRun() {
        $this->updateIndexes([], self::INDEX_LFT, $this->target['rgt'], null, 2);
        $this->updateIndexes([], self::INDEX_RGT, $this->target['rgt'], null, 2);
        $this->insertSingleNode($this->target['rgt'], $this->target['dpt'] + 1);
    }

}
