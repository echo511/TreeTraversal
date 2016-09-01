<?php

namespace Echo511\TreeTraversal\Operations;

use Echo511\TreeTraversal\Tree;
use Exception;
use FluentLiteral;

abstract class Base
{

    const INDEX_LFT = 'lft';
    const INDEX_RGT = 'rgt';

    /**
     * Indexed and depth of target node.
     * @var array
     */
    protected $target = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var Tree
     */
    protected $tree;

    /**
     * @param array $target
     * @param array $config
     * @param Tree $tree
     */
    public function __construct($target, array $config, Tree $tree)
    {
        $this->target = $target;
        $this->config = $config;
        $this->tree = $tree;
    }

    /**
     * Run the operation in transaction.
     */
    public function run()
    {
        $config = $this->config;
        try {
            $this->getFluent()->getPdo()->beginTransaction();
            $this->tree->getFluent()->getPdo()->exec("LOCK TABLES $config[table] WRITE;");
            $return = $this->doRun();
            $this->getFluent()->getPdo()->commit();
            $this->tree->getFluent()->getPdo()->query("UNLOCK TABLES;");
            return $return;
        } catch (Exception $ex) {
            $this->getFluent()->getPdo()->rollBack();
            $this->tree->getFluent()->getPdo()->query("UNLOCK TABLES;");
            throw $ex;
        }
    }

    abstract protected function doRun();

    /**
     * IDs of shifting nodes.
     * @return mixed
     */
    protected function getShiftingNodes()
    {
        $limits = $this->getShiftingIndexesLimits();
        return $this->getNodesBetween($limits['min'], $limits['max']);
    }

    /**
     * Get the distance between indexes of shifting nodes.
     * @return type
     */
    protected function getShiftingNodesRange()
    {
        $limits = $this->getShiftingIndexesLimits();
        return $limits['max'] - $limits['min'] + 1;
    }

    /**
     * Get IDs of nodes between indexes including parent.
     * @param $min
     * @param $max
     * @return array
     */
    protected function getNodesBetween($min, $max)
    {
        $config = $this->config;
        $query = $this->tree->table()
                ->select(NULL)// fetch only id
                ->select("$config[id] AS id")
                ->where("($config[lft] >= :min AND $config[lft] <= :max) OR ($config[rgt] >= :min AND $config[rgt] <= :max)", [
                    ':min' => $min,
                    ':max' => $max,
                ])
                ->fetchAll();
        $ids = array_map(function ($key) {
            return $key['id'];
        }, $query);
        return $ids;
    }

    /**
     *
     * @param array $nodes
     * @param string $index self::INDEX_LFT | self::INDEX_RGT
     * @param type $lftLimit
     * @param type $rgtLimit
     * @param type $value
     */
    protected function updateIndexes($nodes, $index, $lftLimit, $rgtLimit, $value)
    {
        $config = $this->config;
        $query = $this->getFluent()
                ->update($config['table'])
                ->set($config[$index], new FluentLiteral("$config[$index] + $value"));

        if (!empty($nodes)) {
            $query->where($config['id'], $nodes);
        }

        if (!is_null($lftLimit)) {
            $query->where("$config[$index] >= ?", $lftLimit);
        }

        if (!is_null($rgtLimit)) {
            $query->where("$config[$index] <= ?", $rgtLimit);
        }

        $query->execute();
    }

    /**
     * Update nodes depths.
     * @param $nodes
     * @param $value
     */
    protected function updateDepths($nodes, $value)
    {
        $config = $this->config;
        $this->getFluent()
                ->update($config['table'])
                ->set($config['dpt'], new FluentLiteral("$config[dpt] + $value"))
                ->where($config['id'], $nodes)
                ->execute();
    }

    /**
     * Set node the new parent.
     * @param mixed $nodeId
     * @param mixed $parent
     */
    protected function updateNodeParent($nodeId, $parent)
    {
        $config = $this->config;
        $this->getFluent()
                ->update($config['table'])
                ->set($config['prt'], $parent)
                ->where($config['id'], $nodeId)
                ->execute();
    }

    protected function getFluent()
    {
        return $this->tree->getFluent();
    }

}
