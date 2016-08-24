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
    public function __construct(array $config, PDO $pdo) {
        $this->config = array_replace($this->config, $config);
        $this->fluentPdo = new FluentPDO($pdo);
    }

    public function insertNode($data, $targetId = NULL, $mode = self::MODE_UNDER) {
        switch ($mode) {
            case self::MODE_BEFORE:
                break;
            case self::MODE_AFTER:
                break;
            case self::MODE_UNDER:
                break;
        }
    }

    /**
     * Move head node to target node.
     * @param mixed $headId
     * @param mixed $targetId
     * @param int $mode
     */
    public function moveNode($headId, $targetId = NULL, $mode = self::MODE_UNDER) {
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

    public function deleteNode($id) {
        
    }

    /**
     * Is head node child of target node?
     * @param type $head
     * @param type $target
     */
    public function isChildOf($head, $target) {
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
     * @return FluentPDO
     * @internal
     */
    public function getFluent() {
        return $this->fluentPdo;
    }

    /**
     * @return SelectQuery
     * @internal
     */
    public function table() {
        return $this->fluentPdo->from($this->config['table']);
    }

    /**
     * Return single node tree data.
     * @param mixed $id
     * @return array
     */
    protected function getNode($id) {
        $config = $this->config;
        return $this->table()
                        ->select(null)
                        ->select("$config[id] AS id, $config[lft] AS lft, $config[rgt] AS rgt, $config[dpt] AS dpt")
                        ->where($config['id'], $id)
                        ->fetch();
    }

}
