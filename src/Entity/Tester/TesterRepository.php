<?php

namespace Acme\Entity\Tester;

use Acme\DbConnection;

/**
 * Class TesterRepository
 * @package Acme\Entity\Tester
 */
class TesterRepository implements TesterRepositoryInterface
{
    /** @var DbConnection */
    private $db;

    /**
     * TesterRepository constructor.
     * @param DbConnection $dbConnection
     */
    public function __construct(DbConnection $dbConnection)
    {
        $this->db = $dbConnection;
        $this->createSchema();
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
    public function getNextById(int $id): TesterDTO
    {
        $testerStmt = $this->db->getConnection()->prepare('
            SELECT *
            FROM tester
            WHERE id > :id
            LIMIT 1
        ');
        $testerStmt->bindValue(':id', $id);

        $tester = $testerStmt->execute()->fetchArray(SQLITE3_ASSOC);

        if (empty($tester)) {
            $tester = $this->db->getConnection()->querySingle('
                SELECT *
                FROM tester
            ', true);
        }

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