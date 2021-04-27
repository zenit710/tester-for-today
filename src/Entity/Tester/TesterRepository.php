<?php

namespace Acme\Entity\Tester;

use Acme\DbConnection;
use Acme\Entity\NoResultException;
use Acme\Entity\Member\MemberDTO;

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
    }

    /**
     * @inheritDoc
     */
    public function getLastTester(): MemberDTO
    {
        $last = $this->db->getConnection()->querySingle('
            SELECT *
            FROM tester
            JOIN member ON member_id = member.id
            ORDER BY tester.id DESC
        ', true);

        if (empty($last)) {
            throw new NoResultException('Cannot fetch last tester.');
        }

        return MemberDTO::fromArray($last);
    }

    /**
     * @inheritDoc
     */
    public function add(TesterDTO $tester)
    {
        $testerStmt = $this->db->getConnection()->prepare('
            INSERT INTO tester (member_id, date)
            VALUES (:id, :date)
        ');
        $testerStmt->bindValue(':id', $tester->memberId);
        $testerStmt->bindValue(':date', $tester->date);

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