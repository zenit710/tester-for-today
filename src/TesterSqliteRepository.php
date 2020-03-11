<?php

namespace Acme;

use Acme\Logger\Logger;

/**
 * Class TesterSqliteRepository
 * @package Acme
 */
class TesterSqliteRepository {
    const DB_FILE = ROOTPATH . '/tester.db';

    /** @var TesterFileRepository */
    private static $instance = null;

    /** @var Logger */
    private $logger;

    private $connection;

    /**
     * TesterSqliteRepository constructor.
     */
    private function __construct()
    {
        $this->logger = Logger::getInstance();
        $this->connection = new \SQLite3(self::DB_FILE);
        $this->createSchema();
    }

    private function createSchema()
    {
        $this->connection->exec(
        'CREATE TABLE IF NOT EXISTS team (
                name TEXT NOT NULL,
                active INTEGER NOT NULL DEFAULT 1
            )'
        );
        $this->connection->exec(
        'CREATE TABLE IF NOT EXISTS tester_history (
                tester_id INTEGER NOT NULL,
                date TEXT NOT NULL,
                FOREIGN KEY (tester_id)
                    REFERENCES team (rowid)
            )'
        );
        $this->connection->exec(
        'INSERT INTO team (name)
            VALUES ("test")'
        );
        $this->connection->exec(
        'INSERT INTO tester_history (tester_id, date)
            VALUES (1, )'
        );
    }

    /**
     * @return TesterFileRepository
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new TesterSqliteRepository();
        }

        return self::$instance;
    }

    public function getCurrent()
    {
        return $this->connection->querySingle(
            'SELECT * 
                FROM tester_history 
                INNER JOIN team ON tester_id = team.rowid 
                ORDER team_history.rowid DESC'
        );
    }

    public function setCurrentTesterId($id)
    {
        if ($id > $this->getTeamSize()) {
            $this->logger->alert('Tester ' . $id . ' not exist!');
        }

        $this->currentTesterId = $id;
        $this->saveCurrentTesterId();
    }

    public function getNext()
    {
        return $this->team[$this->getNextTesterId()];
    }

    public function getNextTesterId()
    {
        return ($this->currentTesterId + 1) % $this->getTeamSize();
    }

    public function getTeamSize()
    {
        return count($this->team);
    }
}