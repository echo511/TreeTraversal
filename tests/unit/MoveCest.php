<?php

use Echo511\TreeTraversal\Tree;

class MoveCest
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

    public function testMoveParentUnderChild(UnitTester $I)
    {
        $I->expectException('\Echo511\TreeTraversal\InvalidMoveExpcetion', function() {
            $this->tree->moveNode('A', 'E', Tree::MODE_UNDER);
        });
    }

    public function testMoveUnderEndLeft(UnitTester $I)
    {
        $this->tree->moveNode('H', 'D', Tree::MODE_UNDER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
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
                'lft' => 6,
                'rgt' => 7,
                'dpt' => 3,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveUnderEndRight(UnitTester $I)
    {
        $this->tree->moveNode('E', 'A', Tree::MODE_UNDER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 4,
                'rgt' => 7,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 10,
                'rgt' => 15,
                'dpt' => 1,
            ],
            'F' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
            ],
            'G' => [
                'lft' => 13,
                'rgt' => 14,
                'dpt' => 2,
            ],
            'H' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 1,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveAfterLeft(UnitTester $I)
    {
        $this->tree->moveNode('E', 'B', Tree::MODE_AFTER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 10,
                'rgt' => 13,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 4,
                'rgt' => 9,
                'dpt' => 1,
            ],
            'F' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
            ],
            'G' => [
                'lft' => 7,
                'rgt' => 8,
                'dpt' => 2,
            ],
            'H' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveAfterRight(UnitTester $I)
    {
        $this->tree->moveNode('E', 'A', Tree::MODE_AFTER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 10,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 4,
                'rgt' => 7,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 11,
                'rgt' => 16,
                'dpt' => 0,
            ],
            'F' => [
                'lft' => 12,
                'rgt' => 13,
                'dpt' => 1,
            ],
            'G' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
            ],
            'H' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 1,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveBeforeLeft(UnitTester $I)
    {
        $this->tree->moveNode('E', 'A', Tree::MODE_BEFORE);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 7,
                'rgt' => 16,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 10,
                'rgt' => 13,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 1,
                'rgt' => 6,
                'dpt' => 0,
            ],
            'F' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'G' => [
                'lft' => 4,
                'rgt' => 5,
                'dpt' => 1,
            ],
            'H' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveBeforeRight(UnitTester $I)
    {
        $this->tree->moveNode('E', 'H', Tree::MODE_BEFORE);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
                'dpt' => 0,
            ],
            'B' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
            ],
            'C' => [
                'lft' => 4,
                'rgt' => 7,
                'dpt' => 1,
            ],
            'D' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
            ],
            'E' => [
                'lft' => 8,
                'rgt' => 13,
                'dpt' => 1,
            ],
            'F' => [
                'lft' => 9,
                'rgt' => 10,
                'dpt' => 2,
            ],
            'G' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
            ],
            'H' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

}
