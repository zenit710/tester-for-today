<?php

namespace Acme\Entity\Subscriber;

use Acme\DbConnection;

/**
 * Class SubscriberRepository
 * @package Acme\Entity\Subscriber
 */
class SubscriberRepository implements SubscriberRepositoryInterface
{
    /** @var DbConnection */
    private $db;

    /**
     * SubscriberRepository constructor.
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
            CREATE TABLE IF NOT EXISTS subscriber (
                id INTEGER PRIMARY KEY,
                email TEXT NOT NULL UNIQUE,
                active INTEGER NOT NULL DEFAULT 1
            )
        ');
    }

    /**
     * @inheritDoc
     */
    public function getAll(SubscriberFilter $filter = null): array
    {
        $query = 'SELECT * FROM subscriber';

        if (!is_null($filter) && $filter->isAdjusted()) {
            $query .= ' ' . $filter->toWhereClause();
        }

        $subscribers = $this->db->getConnection()->query($query);

        $DTOs = [];
        while ($subscriber = $subscribers->fetchArray(SQLITE3_ASSOC)) {
            $DTOs[] = SubscriberDTO::fromArray($subscriber);
        }

        return $DTOs;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): SubscriberDTO
    {
        $subscriberStmt = $this->db->getConnection()->prepare('
            SELECT *
            FROM subscriber
            WHERE id = :id
        ');
        $subscriberStmt->bindValue(':id', $id);

        $subscriber = $subscriberStmt->execute()->fetchArray(SQLITE3_ASSOC);

        return SubscriberDTO::fromArray($subscriber);
    }

    /**
     * @inheritDoc
     */
    public function add(SubscriberDTO $subscriber)
    {
        $subscriberStmt = $this->db->getConnection()->prepare('
            INSERT INTO subscriber (email)
            VALUES (:email)
        ');
        $subscriberStmt->bindValue(':email', $subscriber->email);

        $subscriberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        $subscriberStmt = $this->db->getConnection()->prepare('
            DELETE FROM subscriber
            WHERE id = :id
        ');
        $subscriberStmt->bindValue(':id', $id);

        $subscriberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function activate(int $id)
    {
        $subscriberStmt = $this->db->getConnection()->prepare('
            UPDATE subscriber 
            SET active = 1
            WHERE id = :id
        ');
        $subscriberStmt->bindValue(':id', $id);

        $subscriberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function deactivate(int $id)
    {
        $subscriberStmt = $this->db->getConnection()->prepare('
            UPDATE subscriber 
            SET active = 0
            WHERE id = :id
        ');
        $subscriberStmt->bindValue(':id', $id);

        $subscriberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->db->getConnection()->exec('DELETE FROM subscriber');
    }
}