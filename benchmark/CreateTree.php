<?php

require_once __DIR__ . '/../vendor/autoload.php';

$config = [
    'table' => 'tree',
    'id' => 'title',
];

$dsn = 'mysql:dbname=tree;host=mysql';
$user = 'root';
$password = '';
$pdo = new PDO($dsn, $user, $password);

$tree = new \Echo511\TreeTraversal\Tree($config, $pdo);

$insertExecutionTimes = [];
global $insertExecutionTimes;

$time_start = microtime(true);
insertNode($tree, null, 4, 5, 0);
$time_end = microtime(true);
$execution_time = ($time_end - $time_start);
echo "Total Execution Time: " . $execution_time . " seconds\n";

$sum = 0;
foreach ($insertExecutionTimes as $time) {
    $sum += $time;
}
$average = $sum / count($insertExecutionTimes);
echo "Average Insert Execution Time: " . $average . " seconds\n";
echo "Maximum Insert Execution Time: " . max($insertExecutionTimes) . " seconds\n";
echo "Minimum Insert Execution Time: " . min($insertExecutionTimes) . " seconds\n";


function insertNode(\Echo511\TreeTraversal\Tree $tree, $parent, $childrenCount = 5, $maxDepth = 5, $depth = 0)
{
    global $insertExecutionTimes;
    $x = 1;
    for ($x; $x <= $childrenCount; $x++) {
        $time_start = microtime(true);
        $tree->insertNode($parent, $parent . $x, \Echo511\TreeTraversal\Tree::MODE_BEFORE);
        $time_end = microtime(true);
        $insertExecutionTimes[] = ($time_end - $time_start);
        if ($depth < $maxDepth) {
            insertNode($tree, $parent . $x, $childrenCount, $maxDepth, $depth + 1);
        }
    }
}
