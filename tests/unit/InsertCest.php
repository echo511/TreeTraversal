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

    protected function getActual($columns = 'title AS id, lft, rgt, dpt, prt')
    {
        $sth = $this->pdo->prepare("SELECT $columns FROM tree");
        $sth->execute();
        return array_map('reset', $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC));
    }

    protected function truncateTree()
    {
        $this->pdo->prepare("TRUNCATE tree")->execute();
    }

    public function testInsertAdditionalColumnsAndReturnedValued(UnitTester $I)
    {
        $expected = [
            'A' => [
                'additional' => 'ADDITIONAL_A'
            ],
            'B' => [
                'additional' => 'ADDITIONAL_B'
            ],
            'C' => [
                'additional' => 'ADDITIONAL_C'
            ]
        ];

        $this->truncateTree();
        $returnedA = $this->tree->insertNode(null, 'A', Tree::MODE_AFTER, ['additional' => 'ADDITIONAL_A']);
        $returnedB = $this->tree->insertNode(null, 'B', Tree::MODE_BEFORE, ['additional' => 'ADDITIONAL_B']);
        $returnedC = $this->tree->insertNode(null, 'C', Tree::MODE_UNDER, ['additional' => 'ADDITIONAL_C']);
        $I->assertEquals($expected, $this->getActual('title AS id, additional'));
        $I->assertEquals('A', $returnedA);
        $I->assertEquals('B', $returnedB);
        $I->assertEquals('C', $returnedC);
    }

    public function testInsertIntoEmptyTree(UnitTester $I)
    {
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 2,
                'dpt' => 0,
                'prt' => null,
            ]
        ];

        $this->truncateTree();
        $this->tree->insertNode(null, 'A');
        $I->assertEquals($expected, $this->getActual());

        $this->truncateTree();
        $this->tree->insertNode(null, 'A', Tree::MODE_AFTER);
        $I->assertEquals($expected, $this->getActual());

        $this->truncateTree();
        $this->tree->insertNode(null, 'A', Tree::MODE_BEFORE);
        $I->assertEquals($expected, $this->getActual());
    }

    public function testInsertFlow(UnitTester $I)
    {
        $this->truncateTree();
        $this->tree->insertNode(null, 'A');
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 2,
                'dpt' => 0,
                'prt' => null,
            ]
        ];
        $I->assertEquals($expected, $this->getActual());

        $this->tree->insertNode(null, 'B');
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 2,
                'dpt' => 0,
                'prt' => null,
            ],
            'B' => [
                'lft' => 3,
                'rgt' => 4,
                'dpt' => 0,
                'prt' => null,
            ]
        ];
        $I->assertEquals($expected, $this->getActual());

        $this->tree->insertNode(null, 'C', Tree::MODE_AFTER);
        $expected = [
            'A' => [
                'lft' => 1,
                'rgt' => 2,
                'dpt' => 0,
                'prt' => null,
            ],
            'B' => [
                'lft' => 3,
                'rgt' => 4,
                'dpt' => 0,
                'prt' => null,
            ],
            'C' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 0,
                'prt' => null,
            ]
        ];
        $I->assertEquals($expected, $this->getActual());

        $this->tree->insertNode(null, 'D', Tree::MODE_BEFORE);
        $expected = [
            'D' => [
                'lft' => 1,
                'rgt' => 2,
                'dpt' => 0,
                'prt' => null,
            ],
            'A' => [
                'lft' => 3,
                'rgt' => 4,
                'dpt' => 0,
                'prt' => null,
            ],
            'B' => [
                'lft' => 5,
                'rgt' => 6,
                'dpt' => 0,
                'prt' => null,
            ],
            'C' => [
                'lft' => 7,
                'rgt' => 8,
                'dpt' => 0,
                'prt' => null,
            ]
        ];
        $I->assertEquals($expected, $this->getActual());
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
