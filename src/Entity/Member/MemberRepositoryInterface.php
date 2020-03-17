<?php

namespace Acme\Entity\Member;

/**
 * Interface MemberRepositoryInterface
 * @package Acme\Entity\Member
 */
interface MemberRepositoryInterface
{
    public function createSchema();

    /**
     * @return MemberDTO[]
     */
    public function getAll(): array;

    /**
     * @param int $id
     * @return MemberDTO
     */
    public function getById(int $id): MemberDTO;

    /**
     * @param int $id
     * @return MemberDTO
     */
    public function getNextById(int $id): MemberDTO;

    /**
     * @param MemberDTO $tester
     */
    public function add(MemberDTO $tester);

    /**
     * @param integer $id
     */
    public function delete(int $id);

    /**
     * @param int $id
     */
    public function activate(int $id);

    /**
     * @param int $id
     */
    public function deactivate(int $id);

    public function clear();
}