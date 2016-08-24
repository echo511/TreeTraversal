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
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 18,
                'dpt' => 0,
                'prt' => null,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'C' => [
                'lft' => 6,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'D' => [
                'lft' => 7,
                'rgt' => 8,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'E' => [
                'lft' => 9,
                'rgt' => 14,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'F' => [
                'lft' => 10,
                'rgt' => 11,
                'dpt' => 3,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 12,
                'rgt' => 13,
                'dpt' => 3,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 16,
                'rgt' => 17,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'I' => [
                'lft' => 4,
                'rgt' => 5,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testInsertAfter(UnitTester $I)
    {
        $this->tree->insertNode('C', 'I', Tree::MODE_AFTER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 18,
                'dpt' => 0,
                'prt' => null,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'C' => [
                'lft' => 4,
                'rgt' => 13,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'D' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'E' => [
                'lft' => 7,
                'rgt' => 12,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'F' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 3,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 10,
                'rgt' => 11,
                'dpt' => 3,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 16,
                'rgt' => 17,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'I' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testInsertUnderEnd(UnitTester $I)
    {
        $this->tree->insertNode('E', 'I', Tree::MODE_UNDER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 18,
                'dpt' => 0,
                'prt' => null,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'C' => [
                'lft' => 4,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'D' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'E' => [
                'lft' => 7,
                'rgt' => 14,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'F' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 3,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 10,
                'rgt' => 11,
                'dpt' => 3,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 16,
                'rgt' => 17,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'I' => [
                'lft' => 12,
                'rgt' => 13,
                'dpt' => 3,
                'prt' => 'E',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

}
