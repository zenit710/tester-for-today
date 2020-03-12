<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

use \Acme\TesterCommand;

if (count($argv) < 2) {
    echo "za mało patametrów" . PHP_EOL . "php app.php (next|choose) [who]" . PHP_EOL;
}

$task = $argv[1];
$who = $argv[2] ?? null;

$command = new TesterCommand();
$command->run($task, $who);

//$repo = \Acme\Entity\Tester\TesterRepository::getInstance();
//$tester = new \Acme\Entity\Tester\TesterDTO();
//$tester->name = "Janusz";
//
//$historyRepo = \Acme\Entity\TestHistory\TestHistoryRepository::getInstance();
//$history = new \Acme\Entity\TestHistory\TestHistoryDTO();
//$history->testerId = 1;
//$history->date = date('Y-m-d');
//
//$repo->createSchema();
//$repo->clear();
//$repo->add($tester);
//
//$historyRepo->createSchema();
//$historyRepo->clear();
//$historyRepo->add($history);
//var_dump($historyRepo->getLast());