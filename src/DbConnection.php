<?php

namespace Acme;

use \SQLite3;

/**
 * Class DbConnection
 * @package Acme
 */
class DbConnection
{
    const DB_FILE = ROOTPATH . '/db/tester.db';

    /** @var DbConnection */
    private static $instance = null;

    /** @var SQLite3 */
    private $connection;

    /**
     * DbConnection constructor.
     */
    private function __construct()
    {
        $this->connection = new SQLite3(self::DB_FILE);
    }

    /**
     * @return DbConnection
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new DbConnection();
        }

        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}