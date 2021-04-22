<?php
date_default_timezone_set('Europe/Warsaw');
define('ROOTPATH', __DIR__);

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$mailUser = getenv('GMAIL_USER');
$mailUserName = getenv('GMAIL_USER_NAME');
$mailPass = getenv('GMAIL_PASS');

$kernel = \Acme\AppKernel::getInstance();

$kernel->addParameter('gmail_user', getenv('GMAIL_USER') ?: '');
$kernel->addParameter('gmail_user_name', getenv('GMAIL_USER_NAME') ?: '');
$kernel->addParameter('gmail_password', getenv('GMAIL_PASS') ?: '');
$kernel->bootstrap();

echo $kernel->handle($argv[1], $argv);