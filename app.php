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

//$repo->clear();
//echo "create schema" . PHP_EOL;
//$repo->createSchema();
//var_dump($repo->getAll());
//echo "add" . PHP_EOL;
//$repo->add($tester);
//var_dump($repo->getAll());
//echo "get by id (2)" . PHP_EOL;
//var_dump($repo->getById(2));
//echo "deactivate (1)" . PHP_EOL;
//$repo->deactivate(1);
//var_dump($repo->getAll());
//echo "activate(1)" . PHP_EOL;
//$repo->activate(1);
//var_dump($repo->getAll());
//echo "delete (1)" . PHP_EOL;
//$repo->delete(1);
//var_dump($repo->getAll());