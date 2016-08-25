<?php

namespace Echo511\TreeTraversal\Operations;

abstract class InsertBase extends Base
{

    protected $insertId = null;

    public function __construct($insertId, $target, array $config, \Echo511\TreeTraversal\Tree $tree)
    {
        parent::__construct($target, $config, $tree);
        $this->insertId = $insertId;
    }

    protected function insertSingleNode($lft, $dpt, $prt)
    {
        $config = $this->config;
        $data = [
            $config['lft'] => $lft,
            $config['rgt'] => $lft + 1,
            $config['dpt'] => $dpt,
            $config['prt'] => $prt,
        ];
        if ($this->insertId !== null) {
            $data[$config['id']] = $this->insertId;
        };
        $this->getFluent()
                ->insertInto($config['table'], $data)->execute();
    }

}
