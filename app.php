<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$kernel = \Acme\AppKernel::getInstance();
$kernel->bootstrap();

$kernel->handle($argv[1], $argv);

$kernel->handle('member:clear', []);
$kernel->handle('subscriber:clear', []);
$kernel->handle('subscriber:add', ['--email=kamil_malek@tvn.pl']);
$kernel->handle('member:add', ['--name=Janusz']);
$kernel->handle('member:add', ['--name=Bonaventura']);
$kernel->handle('member:status', ['--id=1']);
$kernel->handle('member:list', []);
$kernel->handle('tester:clear', []);
$kernel->handle('tester:switch', ['--auto']);
$kernel->handle('tester:current', []);
$kernel->handle('tester:switch', ['--manual', '--id=2']);
$kernel->handle('tester:current', []);
$kernel->handle('tester:switch', ['--manual', '--id=1']);
$kernel->handle('tester:current', []);
//$kernel->handle('switch:tester', ['--auto']);
//$kernel->handle('test-history:current', []);
//$kernel->handle('switch:tester', ['--auto']);
//$kernel->handle('test-history:current', []);