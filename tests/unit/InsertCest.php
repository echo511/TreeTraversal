<?php

use Echo511\TreeTraversal\Tree;

class InsertCest
{

    /**
     * @var Tree
     */
    private $tree;

    /**
     * @var PDO
     */
    private $pdo;

    public function _before(UnitTester $I)
    {
        $dsn = 'mysql:dbname=tree;host=mysql';
        $user = 'root';
        $password = '';

        $this->pdo = new PDO($dsn, $user, $password);

        $config = [
            'table' => 'tree',
            'id' => 'title'
        ];
        $this->tree = new Tree($config, $this->pdo);
    }

    public function _after(UnitTester $I)
    {
        
    }

    public function testInsertBefore(UnitTester $I)
    {
        $this->tree->insertNode('C', 'I', Tree::MODE_BEFORE);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 18,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 6,
                'rgt' => 15,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 7,
                'rgt' => 8,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 9,
                'rgt' => 14,
                'dpt' => 2,
            ],
            'F' => [
                'lft' => 10,
                'rgt' => 11,
                'dpt' => 3,
            ],
            'G' => [
                'lft' => 12,
                'rgt' => 13,
                'dpt' => 3,
            ],
            'H' => [
                'lft' => 16,
                'rgt' => 17,
                'dpt' => 1,
            ],
            'I' => [
                'lft' => 4,
                'rgt' => 5,
                'dpt' => 1,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testInsertAfter(UnitTester $I)
    {
        $this->tree->insertNode('C', 'I', Tree::MODE_AFTER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 18,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 4,
                'rgt' => 13,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 7,
                'rgt' => 12,
                'dpt' => 2,
            ],
            'F' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 3,
            ],
            'G' => [
                'lft' => 10,
                'rgt' => 11,
                'dpt' => 3,
            ],
            'H' => [
                'lft' => 16,
                'rgt' => 17,
                'dpt' => 1,
            ],
            'I' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testInsertUnderEnd(UnitTester $I)
    {
        $this->tree->insertNode('E', 'I', Tree::MODE_UNDER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 18,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 4,
                'rgt' => 15,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 7,
                'rgt' => 14,
                'dpt' => 2,
            ],
            'F' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 3,
            ],
            'G' => [
                'lft' => 10,
                'rgt' => 11,
                'dpt' => 3,
            ],
            'H' => [
                'lft' => 16,
                'rgt' => 17,
                'dpt' => 1,
            ],
            'I' => [
                'lft' => 12,
                'rgt' => 13,
                'dpt' => 3,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

}
