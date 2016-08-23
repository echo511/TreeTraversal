<?php

namespace Echo511\TreeTraversal\Operations;

abstract class MoveBase
{

	const INDEX_LFT = 'lft';
	const INDEX_RGT = 'rgt';

	/*
	 * Direction constants are used as math coefficients.
	 */
	const MOVE_DIRECTION_LEFT = 1; // because shifting nodes indexes increase values => hence 1 => DO NOT CHANGE!
	const MOVE_DIRECTION_RIGHT = -1; // because shifting nodes indexes decrease values => hence -1 => DO NOT CHANGE!

	/**
	 * Indexed and depth of head node.
	 * @var array
	 */

	protected $head = [];

	/**
	 * Indexed and depth of target node.
	 * @var array
	 */
	protected $target = [];

	/**
	 * @var array
	 */
	protected $config = [];

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * MoveUnderEnd constructor.
	 * @param array $head
	 * @param array $target
	 * @param array $config
	 * @param \PDO $pdo
	 */
	public function __construct(array $head, array $target, array $config, \PDO $pdo)
	{
		$this->head = $head;
		$this->target = $target;
		$this->config = $config;
		$this->pdo = $pdo;
	}

	public function run()
	{
		$this->pdo->beginTransaction();
		$this->doRun();
		$this->pdo->commit();
	}

	protected function doRun()
	{
		$movingNodes = $this->getMovingNodes();
		$movingNodesRange = $this->getMovingNodesRange();
		$shiftingNodes = $this->getShiftingNodes();
		$shiftingNodesRange = $this->getShiftingNodesRange();

		// update moving nodes
		$this->updateIndexes($movingNodes, self::INDEX_LFT, $this->head['lft'], $this->head['rgt'], $shiftingNodesRange * -1 * $this->getMoveDirection());
		$this->updateIndexes($movingNodes, self::INDEX_RGT, $this->head['lft'], $this->head['rgt'], $shiftingNodesRange * -1 * $this->getMoveDirection());
		$this->updateDepths($movingNodes, $this->getDepthModifier());

		// update shifting nodes
		$limits = $this->getShiftingIndexesLimits();
		$this->updateIndexes($shiftingNodes, self::INDEX_LFT, $limits['min'], $limits['max'], $movingNodesRange * $this->getMoveDirection());
		$this->updateIndexes($shiftingNodes, self::INDEX_RGT, $limits['min'], $limits['max'], $movingNodesRange * $this->getMoveDirection());
	}

	/**
	 * Get moving nodes IDs.
	 * @return array
	 */
	protected function getMovingNodes()
	{
		$head = $this->head;
		return $this->getNodesBetween($head['lft'], $head['rgt']);
	}

	/**
	 * Get the distance between indexes of head node.
	 * @return int
	 */
	protected function getMovingNodesRange()
	{
		return $this->head['rgt'] - $this->head['lft'] + 1;
	}

	/**
	 * Get shifting nodes IDs.
	 * @return array
	 */
	abstract protected function getShiftingIndexesLimits();

	abstract protected function getDepthModifier();

	protected function getShiftingNodes()
	{
		$limits = $this->getShiftingIndexesLimits();
		return $this->getNodesBetween($limits['min'], $limits['max']);
	}

	protected function getShiftingNodesRange()
	{
		$limits = $this->getShiftingIndexesLimits();
		return $limits['max'] - $limits['min'] + 1;
	}

	/**
	 * Return direction of movement.
	 *
	 * Values can be used as mathematical coefficients.
	 *
	 * @return int
	 */
	abstract protected function getMoveDirection();

	/**
	 * Get IDs of nodes between indexes including parent.
	 * @param $min
	 * @param $max
	 * @return array
	 */
	protected function getNodesBetween($min, $max)
	{
		$config = $this->config;
		$sql = "SELECT $config[id] AS id FROM $config[table] WHERE ($config[lft] >= :min AND $config[lft] <= :max) OR ($config[rgt] >= :min AND $config[rgt] <= :max);";
		$sth = $this->pdo->prepare($sql);
		$sth->bindParam('min', $min);
		$sth->bindParam('max', $max);
		$sth->execute();
		return (array) $sth->fetchAll(\PDO::FETCH_COLUMN);
	}

	/**
	 * Update nodes indexes.
	 *
	 * @param $nodes
	 * @param string $index
	 * @param $lftLimit Update indexes from including this value.
	 * @param $rgtLimit Update indexes to including this value.
	 * @param $value Update with.
	 * @internal param $valueIndexes
	 * @internal param int $valueDepth
	 */
	protected function updateIndexes($nodes, $index = self::INDEX_LFT, $lftLimit, $rgtLimit, $value)
	{
		$lftLimit = (int) $lftLimit;
		$rgtLimit = (int) $rgtLimit;
		if (count($nodes) > 0) {
			$config = $this->config;
			$in = str_repeat('?,', count($nodes) - 1) . '?';
			$sql = "UPDATE $config[table] SET $config[$index] = $config[$index] + $value WHERE $config[$index] >= $lftLimit AND $config[$index] <= $rgtLimit AND $config[id] IN ($in);";
			$stm = $this->pdo->prepare($sql);
			$stm->execute($nodes);
		}
	}

	/**
	 * Update nodes depths.
	 * @param $nodes
	 * @param $value
	 */
	protected function updateDepths($nodes, $value)
	{
		$config = $this->config;
		$in = str_repeat('?,', count($nodes) - 1) . '?';
		$sql = "UPDATE $config[table] SET $config[dpt] = $config[dpt] + $value WHERE $config[id] IN ($in);";
		$stm = $this->pdo->prepare($sql);
		$stm->execute($nodes);
	}

}
