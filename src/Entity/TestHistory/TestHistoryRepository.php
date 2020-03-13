<?php

namespace Acme\Entity\TestHistory;

use Acme\DbConnection;
use Acme\Entity\NoResultException;
use Acme\Entity\Tester\TesterDTO;

/**
 * Class TestHistoryRepository
 * @package Acme\Entity\TestHistory
 */
class TestHistoryRepository implements TestHistoryRepositoryInterface
{
    /** @var DbConnection */
    private $db;

    /**
     * TestHistoryRepository constructor.
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
            CREATE TABLE IF NOT EXISTS tester_history (
                id INTEGER PRIMARY KEY,
                tester_id INTEGER NOT NULL,
                date DATE NOT NULL,
                FOREIGN KEY (tester_id)
                    REFERENCES tester (id)
            )
        ');
    }

    /**
     * @inheritDoc
     */
    public function getLastTester(): TesterDTO
    {
        $last = $this->db->getConnection()->querySingle('
            SELECT *
            FROM tester_history
            JOIN tester ON tester_id = tester.id
            ORDER BY tester_history.id DESC
        ', true);

        if (empty($last)) {
            throw new NoResultException('Cannot fetch last tester.');
        }

        return TesterDTO::fromArray($last);
    }

    /**
     * @inheritDoc
     */
    public function add(TestHistoryDTO $test)
    {
        $historyStmt = $this->db->getConnection()->prepare('
            INSERT INTO tester_history (tester_id, date)
            VALUES (:id, :date)
        ');
        $historyStmt->bindValue(':id', $test->testerId);
        $historyStmt->bindValue(':date', $test->date);

        $historyStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->db->getConnection()->exec('DELETE FROM tester_history');
    }
}