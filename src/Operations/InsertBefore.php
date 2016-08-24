<?php

namespace Echo511\TreeTraversal\Operations;

class InsertBefore extends Base
{

    private $insertId = null;

    public function __construct($insertId, array $target, array $config, \Echo511\TreeTraversal\Tree $tree) {
        parent::__construct($target, $config, $tree);
        $this->insertId = $insertId;
    }

    protected function doRun() {
        $this->updateIndexes([], self::INDEX_LFT, $this->target['lft'], null, 2);
        $this->updateIndexes([], self::INDEX_RGT, $this->target['lft'], null, 2);
        $this->insertSingleNode($this->target['lft'], $this->target['dpt']);
    }

    protected function insertSingleNode($lft, $dpt) {
        $config = $this->config;
        $data = [
            $config['lft'] => $lft,
            $config['rgt'] => $lft + 1,
            $config['dpt'] => $dpt,
        ];
        if ($this->insertId !== null) {
            $data[$config['id']] = $this->insertId;
        };
        $this->getFluent()
                ->insertInto($config['table'], $data)->execute();
    }

}
