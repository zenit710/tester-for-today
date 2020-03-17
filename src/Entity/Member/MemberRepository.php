<?php

namespace Acme\Entity\Member;

use Acme\DbConnection;

/**
 * Class MemberRepository
 * @package Acme\Entity\Member
 */
class MemberRepository implements MemberRepositoryInterface
{
    /** @var DbConnection */
    private $db;

    /**
     * MemberRepository constructor.
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
            CREATE TABLE IF NOT EXISTS member (
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
        $members = $this->db->getConnection()->query('
            SELECT * FROM member
        ');

        $DTOs = [];
        while ($member = $members->fetchArray(SQLITE3_ASSOC)) {
            $DTOs[] = MemberDTO::fromArray($member);
        }

        return $DTOs;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): MemberDTO
    {
        $memberStmt = $this->db->getConnection()->prepare('
            SELECT * FROM member WHERE id = :id
        ');
        $memberStmt->bindValue(':id', $id);

        $member = $memberStmt->execute()->fetchArray(SQLITE3_ASSOC);

        return MemberDTO::fromArray($member);
    }

    /**
     * @inheritDoc
     */
    public function getNextById(int $id): MemberDTO
    {
        $memberStmt = $this->db->getConnection()->prepare('
            SELECT *
            FROM member
            WHERE id > :id
            LIMIT 1
        ');
        $memberStmt->bindValue(':id', $id);

        $member = $memberStmt->execute()->fetchArray(SQLITE3_ASSOC);

        if (empty($member)) {
            $member = $this->db->getConnection()->querySingle('
                SELECT *
                FROM member
            ', true);
        }

        return MemberDTO::fromArray($member);
    }

    /**
     * @inheritDoc
     */
    public function add(MemberDTO $member)
    {
        $memberStmt = $this->db->getConnection()->prepare('
            INSERT INTO member (name)
            VALUES (:name)
        ');
        $memberStmt->bindValue(':name', $member->name);

        $memberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id)
    {
        $memberStmt = $this->db->getConnection()->prepare('
            DELETE FROM member
            WHERE id = :id
        ');
        $memberStmt->bindValue(':id', $id);

        $memberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function activate(int $id)
    {
        $memberStmt = $this->db->getConnection()->prepare('
            UPDATE member 
            SET active = 1
            WHERE id = :id
        ');
        $memberStmt->bindValue(':id', $id);

        $memberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function deactivate(int $id)
    {
        $memberStmt = $this->db->getConnection()->prepare('
            UPDATE member 
            SET active = 0
            WHERE id = :id
        ');
        $memberStmt->bindValue(':id', $id);

        $memberStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->db->getConnection()->exec('DELETE FROM member');
    }
}