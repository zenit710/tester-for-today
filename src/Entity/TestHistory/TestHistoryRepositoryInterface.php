<?php

namespace Acme\Entity\TestHistory;

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
     */
    public function getLast(): TesterDTO;

    /**
     * @param TestHistoryDTO $test
     */
    public function add(TestHistoryDTO $test);

    public function clear();
}