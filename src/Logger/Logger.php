<?php

namespace Acme\Logger;

use \Monolog\Logger as Monolog;
use \Monolog\Handler\StreamHandler;

/**
 * Class Logger
 * @package Acme\Logger
 */
class Logger
{
    /**
     * @var Logger | null
     */
    private static $logger = null;

    /**
     * @var string
     */
    private static $logPath = '.';

    /**
     * @var Monolog
     */
    private $log;

    /**
     * Logger constructor.
     */
    private function __construct() {
        $this->log = new Monolog('log');
        $this->log->pushHandler(new StreamHandler(self::$logPath . '/app.log'));
    }

    private function __clone() {}

    /**
     * @return Logger
     */
    public static function getInstance() {
        if (is_null(self::$logger)) {
            self::$logger = new Logger();
        }

        return self::$logger;
    }

    public static function setLogDirectory($path) {
        self::$logPath = $path;
    }

    public function debug($message, array $context = array()) {
        $this->log->debug($message, $context);
    }

    public function info($message, array $context = array()) {
        $this->log->info($message, $context);
    }

    public function notice($message, array $context = array()) {
        $this->log->notice($message, $context);
    }

    public function warning($message, array $context = array()) {
        $this->log->warning($message, $context);
    }

    public function error($message, array $context = array()) {
        $this->log->error($message, $context);
    }

    public function critical($message, array $context = array()) {
        $this->log->critical($message, $context);
    }

    public function alert($message, array $context = array()) {
        $this->log->alert($message, $context);
    }

    public function emergency($message, array $context = array()) {
        $this->log->emergency($message, $context);
    }
}