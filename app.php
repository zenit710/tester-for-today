<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$container = new \Acme\Container();
$container->bootstrap();

$testerRepo = $container->getService('TesterRepository');
$testerRepo->clear();
$container->handle($argv[1], $argv);

$container->handle('tester:add', ['--name=Bonaventura']);
$container->handle('tester:list', []);
$container->handle('tester:status', ['--id=1']);
$container->handle('tester:list', []);
$container->handle('tester:status', ['--id=1', '--active']);
$container->handle('tester:list', []);
$container->handle('tester:status', ['--id=1', '--inactive']);
$container->handle('tester:list', []);
$container->handle('tester:delete', ['--id=1']);
$container->handle('tester:list', []);