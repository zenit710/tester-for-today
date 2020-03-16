<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$kernel = \Acme\AppKernel::getInstance();
$kernel->bootstrap();

$kernel->handle($argv[1], $argv);

$kernel->handle('tester:clear', []);
$kernel->handle('subscriber:clear', []);
$kernel->handle('subscriber:add', ['--email=kamil_malek@tvn.pl']);
$kernel->handle('tester:add', ['--name=Janusz']);
$kernel->handle('tester:add', ['--name=Bonaventura']);
$kernel->handle('test-history:clear', []);
$kernel->handle('switch:tester', ['--auto']);
$kernel->handle('test-history:current', []);
$kernel->handle('switch:tester', ['--manual', '--id=2']);
$kernel->handle('test-history:current', []);
$kernel->handle('switch:tester', ['--auto']);
$kernel->handle('test-history:current', []);
$kernel->handle('switch:tester', ['--auto']);
$kernel->handle('test-history:current', []);