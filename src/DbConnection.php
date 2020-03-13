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

    /** @var SQLite3 */
    private $connection;

    /**
     * DbConnection constructor.
     */
    public function __construct()
    {
        $this->connection = new SQLite3(self::DB_FILE);
    }

    public function getConnection() {
        return $this->connection;
    }
}