<?php

namespace Echo511\TreeTraversal\Operations;

use Echo511\TreeTraversal\Tree;

class Delete extends Base
{

    protected $node = [];

    public function __construct(array $node, array $config, Tree $tree) {
        parent::__construct([], $config, $tree);
        $this->node = $node;
    }

    protected function doRun() {
        $this->deleteNodeAndChildren($this->node);
    }

    protected function deleteNodeAndChildren($node) {
        $config = $this->config;
        $this->getFluent()
                ->delete($config['table'])
                ->where("$config[lft] >= ?", $node['lft'])
                ->where("$config[rgt] <= ?", $node['rgt'])
                ->execute();
        $deletingNodeRange = $node['rgt'] - $node['lft'] + 1;
        $this->updateIndexes([], self::INDEX_LFT, $node['rgt'] + 1, null, -1 * $deletingNodeRange);
        $this->updateIndexes([], self::INDEX_RGT, $node['rgt'] + 1, null, -1 * $deletingNodeRange);
    }

}
