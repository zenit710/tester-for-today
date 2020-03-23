<?php

namespace Acme\Entity\Absence;

use Acme\Entity\NoResultException;
use Acme\Entity\NothingToUpdateException;

/**
 * Interface AbsenceRepositoryInterface
 * @package Acme\Entity\Absence
 */
interface AbsenceRepositoryInterface
{
    /**
     * @param AbsenceDTO $absence
     */
    public function add(AbsenceDTO $absence);

    /**
     * @param AbsenceFilter $filter
     * @return AbsenceDTO[]
     */
    public function getAll(AbsenceFilter $filter = null): array;

    /**
     * @param int $id
     * @return AbsenceDTO
     * @throws NoResultException
     */
    public function getById(int $id): AbsenceDTO;

    /**
     * @param int $id
     * @throws NothingToUpdateException
     */
    public function cancel(int $id);

    /**
     * @param int $id
     * @throws NothingToUpdateException
     */
    public function restore(int $id);

    public function clear();
}