<?php
date_default_timezone_set('Europe/Warsaw');

require __DIR__ . '/vendor/autoload.php';

use \Acme\Logger\Logger;

$logDirectory = __DIR__ . '/.data/_logs';
Logger::setLogDirectory($logDirectory);
$log = Logger::getInstance();
$log->info('app is runing');