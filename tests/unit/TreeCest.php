<?php

use Echo511\TreeTraversal\Tree;

class TreeCest
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

    public function testIsChildOf(UnitTester $I)
    {
        $I->assertTrue($this->tree->isChildOf('B', 'A'));
        $I->assertTrue($this->tree->isChildOf('C', 'A'));
        $I->assertTrue($this->tree->isChildOf('D', 'A'));
        $I->assertTrue($this->tree->isChildOf('E', 'A'));
        $I->assertTrue($this->tree->isChildOf('F', 'A'));
        $I->assertTrue($this->tree->isChildOf('G', 'A'));
        $I->assertTrue($this->tree->isChildOf('H', 'A'));

        $I->assertTrue($this->tree->isChildOf('F', 'E'));
        $I->assertTrue($this->tree->isChildOf('G', 'E'));

        $I->assertTrue($this->tree->isChildOf('F', 'C'));
        $I->assertTrue($this->tree->isChildOf('G', 'C'));

        $I->assertFalse($this->tree->isChildOf('A', 'A'));
        $I->assertFalse($this->tree->isChildOf('A', 'B'));
        $I->assertFalse($this->tree->isChildOf('A', 'C'));
        $I->assertFalse($this->tree->isChildOf('A', 'D'));
        $I->assertFalse($this->tree->isChildOf('A', 'E'));
        $I->assertFalse($this->tree->isChildOf('A', 'F'));
        $I->assertFalse($this->tree->isChildOf('A', 'G'));
    }

    public function testGetChildren(UnitTester $I)
    {
        $expected = ['B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $I->assertEquals($expected, $this->tree->getChildren('A'));

        $expected = ['F', 'G'];
        $I->assertEquals($expected, $this->tree->getChildren('E'));

        $expected = [];
        $I->assertEquals($expected, $this->tree->getChildren('G'));
    }

    public function testGetParents(UnitTester $I)
    {
        $expected = [];
        $I->assertEquals($expected, $this->tree->getParents('A'));

        $expected = ['A', 'C', 'E'];
        $I->assertEquals($expected, $this->tree->getParents('G'));
    }

}
