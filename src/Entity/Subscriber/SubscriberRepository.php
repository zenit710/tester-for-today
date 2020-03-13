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
                email TEXT NOT NULL UNIQUE
            )
        ');
    }

    /**
     * @inheritDoc
     */
    public function getAll(): array
    {
        $subscribers = $this->db->getConnection()->query('
            SELECT *
            FROM subscriber
        ');

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
    public function clear()
    {
        $this->db->getConnection()->exec('DELETE FROM subscriber');
    }
}