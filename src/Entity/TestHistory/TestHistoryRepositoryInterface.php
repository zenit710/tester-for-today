<?php

namespace Acme\Entity\TestHistory;

use Acme\Entity\NoResultException;
use Acme\Entity\Tester\TesterDTO;

/**
 * Interface TestHistoryRepositoryInterface
 * @package Acme\Entity\TestHistory
 */
interface TestHistoryRepositoryInterface
{
    public function createSchema();

    /**
     * @return TesterDTO
     * @throws NoResultException
     */
    public function getLastTester(): TesterDTO;

    /**
     * @param TestHistoryDTO $test
     */
    public function add(TestHistoryDTO $test);

    public function clear();
}