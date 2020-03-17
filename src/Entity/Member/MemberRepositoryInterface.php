<?php

namespace Acme\Entity\Member;

use Acme\Entity\NoResultException;
use Acme\Entity\NothingToDeleteException;
use Acme\Entity\NothingToUpdateException;

/**
 * Interface MemberRepositoryInterface
 * @package Acme\Entity\Member
 */
interface MemberRepositoryInterface
{
    public function createSchema();

    /**
     * @param MemberFilter|null $filter
     * @return MemberDTO[]
     */
    public function getAll(MemberFilter $filter = null): array;

    /**
     * @param int $id
     * @return MemberDTO
     * @throws NoResultException
     */
    public function getById(int $id): MemberDTO;

    /**
     * @param int $id
     * @return MemberDTO
     * @throws NoResultException
     */
    public function getNextActiveById(int $id): MemberDTO;

    /**
     * @param MemberDTO $tester
     */
    public function add(MemberDTO $tester);

    /**
     * @param integer $id
     * @throws NothingToDeleteException
     */
    public function delete(int $id);

    /**
     * @param int $id
     * @throws NothingToUpdateException
     */
    public function activate(int $id);

    /**
     * @param int $id
     * @throws NothingToUpdateException
     */
    public function deactivate(int $id);

    public function clear();
}