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