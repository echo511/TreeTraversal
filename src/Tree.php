<?php

namespace Echo511\TreeTraversal;

/**
 * Class Tree
 * @package Echo511\TreeTraversal
 */
class Tree
{

	const MODE_BEFORE = 0;
	const MODE_AFTER = 1;
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
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * Tree constructor.
	 * @param array $config
	 * @param \PDO $pdo
	 */
	public function __construct(array $config, \PDO $pdo)
	{
		$this->config = array_replace($this->config, $config);
		$this->pdo = $pdo;
	}

	public function insertNode($data, $targetId = NULL, $mode = self::MODE_UNDER)
	{
		switch ($mode) {
			case self::MODE_BEFORE:
				break;
			case self::MODE_AFTER:
				break;
			case self::MODE_UNDER:
				break;
		}
	}

	public function deleteNode($id)
	{

	}

}
