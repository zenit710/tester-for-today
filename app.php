<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$container = new \Acme\Container();
$container->bootstrap();

$testerRepo = $container->getService('TesterRepository');
$testerRepo->clear();
$container->handle($argv[1], $argv);

var_dump($testerRepo->getAll());