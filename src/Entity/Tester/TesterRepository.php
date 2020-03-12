<?php

namespace Acme\Entity\Tester;

use Acme\DbConnection;

/**
 * Class TesterRepository
 * @package Acme\Entity\Tester
 */
class TesterRepository implements TesterRepositoryInterface
{
    /** @var TesterRepository */
    private static $instance = null;

    /** @var DbConnection */
    private $db;

    /**
     * TesterRepository constructor.
     */
    private function __construct()
    {
        $this->db = DbConnection::getInstance();
        $this->createSchema();
    }

    /**
     * @inheritDoc
     */
    public static function getInstance(): TesterRepositoryInterface
    {
        if (is_null(self::$instance)) {
            self::$instance = new TesterRepository();
        }

        return self::$instance;
    }

    /**
     * @inheritDoc
     */
    public function createSchema()
    {
        $this->db->getConnection()->exec('
            CREATE TABLE IF NOT EXISTS tester (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL UNIQUE,
                active INTEGER NOT NULL DEFAULT 1
            )
        ');
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $testers = $this->db->getConnection()->query('
            SELECT * FROM tester
        ');

        $DTOs = [];
        while ($tester = $testers->fetchArray(SQLITE3_ASSOC)) {
            $DTOs[] = TesterDTO::fromArray($tester);
        }

        return $DTOs;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): TesterDTO
    {
        $testerStmt = $this->db->getConnection()->prepare('
            SELECT * FROM tester WHERE id = :id
        ');
        $testerStmt->bindValue(':id', $id);

        $tester = $testerStmt->execute()->fetchArray(SQLITE3_ASSOC);

        return TesterDTO::fromArray($tester);
    }

    /**
     * @inheritDoc
     */
    public function add(TesterDTO $tester)
    {
        $testerStmt = $this->db->getConnection()->prepare('
            INSERT INTO tester (name)
            VALUES (:name)
        ');
        $testerStmt->bindValue(':name', $tester->name);

        $testerStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        $testerStmt = $this->db->getConnection()->prepare('
            DELETE FROM tester
            WHERE id = :id
        ');
        $testerStmt->bindValue(':id', $id);

        $testerStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function activate(int $id)
    {
        $testerStmt = $this->db->getConnection()->prepare('
            UPDATE tester 
            SET active = 1
            WHERE id = :id
        ');
        $testerStmt->bindValue(':id', $id);

        $testerStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function deactivate(int $id)
    {
        $testerStmt = $this->db->getConnection()->prepare('
            UPDATE tester 
            SET active = 0
            WHERE id = :id
        ');
        $testerStmt->bindValue(':id', $id);

        $testerStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->db->getConnection()->exec('DELETE FROM tester');
    }
}