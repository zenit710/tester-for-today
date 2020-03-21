<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$kernel = \Acme\AppKernel::getInstance();
$kernel->bootstrap();

//if ($argc < 2) {
//    echo $kernel->getCommandBus()->availableCommands();
//    exit;
//}
//
echo $kernel->handle($argv[1], $argv);
//
//// tests
//echo $kernel->handle('member:clear', []);
//echo $kernel->handle('member:add', ['--name=Janusz']);

/** @var \Acme\Entity\Member\MemberRepositoryInterface $memberRepo */
//$memberRepo = $kernel->getService('MemberRepository');
//$memberRepo->getById(2);

//echo $kernel->handle('tester:clear', []);
//echo $kernel->handle('tester:switch', ['--auto']);
//echo $kernel->handle('tester:switch', ['--manual', '--id=1']);
//
//echo $kernel->handle('member:status', ['--id=1']);
//echo $kernel->handle('member:delete', ['--id=1']);
//
//echo $kernel->handle('subscriber:clear', []);
//echo $kernel->handle('subscriber:add', ['--email=kamil_malek@tvn.pl']);
//echo $kernel->handle('subscriber:status', ['--id=1']);
//echo $kernel->handle('subscriber:delete', ['--id=1']);