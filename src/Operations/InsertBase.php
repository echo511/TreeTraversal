<?php

namespace Echo511\TreeTraversal\Operations;

abstract class InsertBase extends Base
{

    protected $insertId = null;

    protected $additionalColumns = [];

    public function __construct($insertId, $target, array $config, \Echo511\TreeTraversal\Tree $tree, array $additionalColumns)
    {
        parent::__construct($target, $config, $tree);
        $this->insertId = $insertId;
        $this->additionalColumns = $additionalColumns;
    }

    protected function insertSingleNode($lft, $dpt, $prt)
    {
        $config = $this->config;
        $data = array_replace($this->additionalColumns, [
            $config['lft'] => $lft,
            $config['rgt'] => $lft + 1,
            $config['dpt'] => $dpt,
            $config['prt'] => $prt,
        ]);
        if ($this->insertId !== null) {
            $data[$config['id']] = $this->insertId;
        };
        $this->getFluent()
                ->insertInto($config['table'], $data)->execute();
    }

}
