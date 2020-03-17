<?php

namespace Acme\Entity\Tester;

use Acme\Entity\NoResultException;
use Acme\Entity\Member\MemberDTO;

/**
 * Interface TesterRepositoryInterface
 * @package Acme\Entity\Tester
 */
interface TesterRepositoryInterface
{
    public function createSchema();

    /**
     * @return MemberDTO
     * @throws NoResultException
     */
    public function getLastTester(): MemberDTO;

    /**
     * @param TesterDTO $tester
     */
    public function add(TesterDTO $tester);

    public function clear();
}