<?php

namespace Acme\Entity\Absence;

use Acme\DbConnection;
use Acme\Entity\NoResultException;
use Acme\Entity\NothingToUpdateException;

/**
 * Class AbsenceRepository
 * @package Acme\Entity\Absence
 */
class AbsenceRepository implements AbsenceRepositoryInterface
{
    /** @var DbConnection */
    private $db;

    /**
     * AbsenceRepository constructor.
     * @param DbConnection $dbConnection
     */
    public function __construct(DbConnection $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * @inheritDoc
     */
    public function add(AbsenceDTO $absence)
    {
        $absenceStmt = $this->db->getConnection()->prepare('
            INSERT INTO absence (member_id, date_from, date_to)
            VALUES (:memberId, :from, :to)
        ');
        $absenceStmt->bindValue(':memberId', $absence->memberId);
        $absenceStmt->bindValue(':from', $absence->dateFrom);
        $absenceStmt->bindValue(':to', $absence->dateTo);

        $absenceStmt->execute();
    }

    /**
     * @inheritDoc
     */
    public function getAll(AbsenceFilter $filter = null): array
    {
        $query = 'SELECT * FROM absence';

        if (!is_null($filter) && $filter->isAdjusted()) {
            $query .= ' ' . $filter->toWhereClause();
        }

        $absences = $this->db->getConnection()->query($query);

        $DTOs = [];
        while ($absence = $absences->fetchArray(SQLITE3_ASSOC)) {
            $DTOs[] = AbsenceDTO::fromArray($absence);
        }

        return $DTOs;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): AbsenceDTO
    {
        $absenceStmt = $this->db->getConnection()->prepare('
            SELECT * FROM absence WHERE id = :id
        ');
        $absenceStmt->bindValue(':id', $id);

        $absence = $absenceStmt->execute()->fetchArray(SQLITE3_ASSOC);

        if (empty($absence)) {
            throw new NoResultException('Cannot find absence id: ' . $id);
        }

        return AbsenceDTO::fromArray($absence);
    }

    /**
     * @inheritDoc
     */
    public function cancel(int $id)
    {
        $absenceStmt = $this->db->getConnection()->prepare('
            UPDATE absence 
            SET canceled = 1
            WHERE id = :id
        ');
        $absenceStmt->bindValue(':id', $id);

        $absenceStmt->execute();

        if ($this->db->getConnection()->changes() == 0) {
            throw new NothingToUpdateException('Absence id: ' . $id . ' not exists');
        }
    }

    /**
     * @inheritDoc
     */
    public function restore(int $id)
    {
        $absenceStmt = $this->db->getConnection()->prepare('
            UPDATE absence 
            SET canceled = 0
            WHERE id = :id
        ');
        $absenceStmt->bindValue(':id', $id);

        $absenceStmt->execute();

        if ($this->db->getConnection()->changes() == 0) {
            throw new NothingToUpdateException('Absence id: ' . $id . ' not exists');
        }
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        $this->db->getConnection()->exec('DELETE FROM absence');
    }

}