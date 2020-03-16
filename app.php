<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$container = \Acme\AppKernel::getInstance();
$container->bootstrap();

$container->handle($argv[1], $argv);

$container->handle('tester:clear', []);
$container->handle('tester:add', ['--name=Janusz']);
$container->handle('tester:add', ['--name=Bonaventura']);
$container->handle('test-history:clear', []);
$container->handle('test-history:add', ['--auto']);
$container->handle('test-history:current', []);
$container->handle('test-history:add', ['--manual', '--id=2']);
$container->handle('test-history:current', []);
$container->handle('test-history:add', ['--auto']);
$container->handle('test-history:current', []);
$container->handle('test-history:add', ['--auto']);
$container->handle('test-history:current', []);