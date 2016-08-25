<?php

namespace Echo511\TreeTraversal;

use Echo511\TreeTraversal\Operations\MoveAfter;
use Echo511\TreeTraversal\Operations\MoveBefore;
use Echo511\TreeTraversal\Operations\MoveUnderEnd;
use FluentPDO;
use PDO;
use SelectQuery;

class Tree
{

    /**
     * Head node placed next to and before the target node.
     */
    const MODE_BEFORE = 0;

    /**
     * Head node placed next to and after the target node.
     */
    const MODE_AFTER = 1;

    /**
     * Head node placed under the target node and at the end of all its children.
     */
    const MODE_UNDER = 2;

    /**
     * @var array
     */
    private $config = [
        'table' => '',
        'id' => 'id', // node id column
        'lft' => 'lft', // left index
        'rgt' => 'rgt', // right index
        'dpt' => 'dpt', // depth
        'prt' => 'prt', // parent - for emergency tree fixup
    ];

    /**
     * @var FluentPDO
     */
    private $fluentPdo;

    /**
     * Tree constructor.
     * @param array $config
     * @param PDO $pdo
     */
    public function __construct(array $config, PDO $pdo)
    {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->config = array_replace($this->config, $config);
        $this->fluentPdo = new FluentPDO($pdo);
    }
    
    public function getFirstRootNode()
    {
        $config = $this->config;
        return $this->table()
                        ->select(null)
                        ->select("$config[id] AS id, $config[lft] AS lft, $config[rgt] AS rgt, $config[dpt] AS dpt, $config[prt] AS prt")
                        ->where($config['dpt'], 0)
                        ->orderBy($config['lft'])
                        ->limit(1)
                        ->fetch();
    }
    
    public function getLastRootNode()
    {
        $config = $this->config;
        return $this->table()
                        ->select(null)
                        ->select("$config[id] AS id, $config[lft] AS lft, $config[rgt] AS rgt, $config[dpt] AS dpt, $config[prt] AS prt")
                        ->where($config['dpt'], 0)
                        ->orderBy("$config[lft] DESC")
                        ->limit(1)
                        ->fetch();        
    }

    public function insertNode($targetId = null, $insertId = null, $mode = self::MODE_UNDER)
    {
        $target = $this->getNode($targetId);

        switch ($mode) {
            case self::MODE_BEFORE:
                $operation = new Operations\InsertBefore($insertId, $target, $this->config, $this);
                break;
            case self::MODE_AFTER:
                $operation = new Operations\InsertAfter($insertId, $target, $this->config, $this);
                break;
            case self::MODE_UNDER:
                $operation = new Operations\InsertUnderEnd($insertId, $target, $this->config, $this);
                break;
        }

        $operation->run();
    }

    /**
     * Move head node to target node.
     * @param mixed $headId
     * @param mixed $targetId
     * @param int $mode
     */
    public function moveNode($headId, $targetId = null, $mode = self::MODE_UNDER)
    {
        $head = $this->getNode($headId);
        $target = $this->getNode($targetId);

        switch ($mode) {
            case self::MODE_BEFORE:
                $operation = new MoveBefore($head, $target, $this->config, $this);
                break;
            case self::MODE_AFTER:
                $operation = new MoveAfter($head, $target, $this->config, $this);
                break;
            case self::MODE_UNDER:
                $operation = new MoveUnderEnd($head, $target, $this->config, $this);
                break;
        }

        $operation->run();
    }

    /**
     * Delete node and its children.
     * @param int $nodeId
     */
    public function deleteNode($nodeId)
    {
        $node = $this->getNode($nodeId);

        $operation = new Operations\Delete($node, $this->config, $this);
        $operation->run();
    }

    /**
     * Is head node child of target node?
     * @param type $head
     * @param type $target
     */
    public function isChildOf($head, $target)
    {
        if (!is_array($head)) {
            $head = $this->getNode($head);
        }
        if (!is_array($target)) {
            $target = $this->getNode($target);
        }
        $config = $this->config;
        $headLft = $head[$config['lft']];
        $headRgt = $head[$config['rgt']];
        $targetLft = $target[$config['lft']];
        $targetRgt = $target[$config['rgt']];
        if ($headLft > $targetLft && $headRgt < $targetRgt) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Get IDs of children nodes.
     * @param mixed $headId
     * @param int|null Eg. 1 - children, 2 - grandchildren.
     * @param bool If enabled and $relativeDepth = 2 then select children and grandchildren.
     * @return array
     */
    public function getChildren($headId, $relativeDepth = null, $summarize = false)
    {
        $head = $this->getNode($headId);
        $config = $this->config;
        $query = $this->table()
                ->select(null)
                ->select("$config[id] AS id")
                ->where("$config[lft] > ?", $head['lft'])
                ->where("$config[rgt] < ?", $head['rgt']);

        if (!is_null($relativeDepth)) {
            $absoluteDepth = $relativeDepth + $head['dpt'];
            if ($summarize) {
                $query->where("$config[dpt] <= ?", $absoluteDepth);
            } else {
                $query->where("$config[dpt] = ?", $absoluteDepth);
            }
        }

        $children = $query->fetchAll();

        return array_map(function($key) {
            return $key['id'];
        }, $children);
    }

    /**
     * Get parents in order, the big boss first.
     * @param type $headId
     * @return type
     */
    public function getParents($headId)
    {
        $head = $this->getNode($headId);
        $config = $this->config;
        $parents = $this->table()
                ->select(null)
                ->select("$config[id] AS id")
                ->where("$config[lft] < ?", $head['lft'])
                ->where("$config[rgt] > ?", $head['rgt'])
                ->where("$config[dpt] < ?", $head['dpt'])
                ->orderBy($config['lft'])
                ->fetchAll();

        return array_map(function($key) {
            return $key['id'];
        }, $parents);
    }

    /**
     * @return FluentPDO
     * @internal
     */
    public function getFluent()
    {
        return $this->fluentPdo;
    }

    /**
     * @return SelectQuery
     * @internal
     */
    public function table()
    {
        return $this->fluentPdo->from($this->config['table']);
    }

    /**
     * Return single node tree data.
     * @param mixed $id
     * @return array
     */
    protected function getNode($id)
    {
        $config = $this->config;
        return $this->table()
                        ->select(null)
                        ->select("$config[id] AS id, $config[lft] AS lft, $config[rgt] AS rgt, $config[dpt] AS dpt, $config[prt] AS prt")
                        ->where($config['id'], $id)
                        ->fetch();
    }

}
