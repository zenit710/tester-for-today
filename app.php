<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$container = \Acme\AppKernel::getInstance();
$container->bootstrap();

$container->handle($argv[1], $argv);

$container->handle('subscriber:clear', []);
$container->handle('subscriber:add', ['--email=jan.kowalski@onet.pl']);
$container->handle('subscriber:add', ['--email=jaroslaw.kowalski@onet.pl']);
$container->handle('subscriber:list', []);
$container->handle('subscriber:status', ['--id=1']);
$container->handle('subscriber:list', []);
$container->handle('subscriber:status', ['--id=1', '--active']);
$container->handle('subscriber:list', []);
$container->handle('subscriber:status', ['--id=1', '--inactive']);
$container->handle('subscriber:list', []);
$container->handle('subscriber:delete', ['--id=1']);
$container->handle('subscriber:list', []);