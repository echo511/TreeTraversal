<?php

use Echo511\TreeTraversal\Tree;

class DeleteCest
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

    public function testDelete(UnitTester $I)
    {
        $this->tree->deleteNode('E');
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
            'H' => [
                'lft' => 8,
                'rgt' => 9,
                'dpt' => 1,
                'prt' => 'A',
            ]
        ];
        $I->assertEquals($expected, $actual);
    }

}
