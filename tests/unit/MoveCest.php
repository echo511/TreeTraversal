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
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
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
                'lft' => 6,
                'rgt' => 7,
                'dpt' => 3,
                'prt' => 'D',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveUnderEndRight(UnitTester $I)
    {
        $this->tree->moveNode('E', 'A', Tree::MODE_UNDER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
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
                'rgt' => 7,
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
                'lft' => 10,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'F' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 13,
                'rgt' => 14,
                'dpt' => 2,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveAfterLeft(UnitTester $I)
    {
        $this->tree->moveNode('E', 'B', Tree::MODE_AFTER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
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
                'lft' => 10,
                'rgt' => 13,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'D' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'E' => [
                'lft' => 4,
                'rgt' => 9,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'F' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 2,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 7,
                'rgt' => 8,
                'dpt' => 2,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveAfterRight(UnitTester $I)
    {
        $this->tree->moveNode('E', 'A', Tree::MODE_AFTER);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 10,
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
                'rgt' => 7,
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
                'lft' => 11,
                'rgt' => 16,
                'dpt' => 0,
                'prt' => null,
            ],
            'F' => [
                'lft' => 12,
                'rgt' => 13,
                'dpt' => 1,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveBeforeLeft(UnitTester $I)
    {
        $this->tree->moveNode('E', 'A', Tree::MODE_BEFORE);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 7,
                'rgt' => 16,
                'dpt' => 0,
                'prt' => null,
            ],
            'B' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'C' => [
                'lft' => 10,
                'rgt' => 13,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'D' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
                'prt' => 'C',
            ],
            'E' => [
                'lft' => 1,
                'rgt' => 6,
                'dpt' => 0,
                'prt' => null,
            ],
            'F' => [
                'lft' => 2,
                'rgt' => 3,
                'dpt' => 1,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 4,
                'rgt' => 5,
                'dpt' => 1,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

    public function testMoveBeforeRight(UnitTester $I)
    {
        $this->tree->moveNode('E', 'H', Tree::MODE_BEFORE);
        $sth = $this->pdo->prepare("SELECT title AS id, lft, rgt, dpt, prt FROM tree");
        $sth->execute();
        $actual = array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 16,
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
                'rgt' => 7,
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
                'lft' => 8,
                'rgt' => 13,
                'dpt' => 1,
                'prt' => 'A',
            ],
            'F' => [
                'lft' => 9,
                'rgt' => 10,
                'dpt' => 2,
                'prt' => 'E',
            ],
            'G' => [
                'lft' => 11,
                'rgt' => 12,
                'dpt' => 2,
                'prt' => 'E',
            ],
            'H' => [
                'lft' => 14,
                'rgt' => 15,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

}
