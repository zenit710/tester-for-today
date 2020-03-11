<?php

namespace Acme\Entity\TestHistory;

/**
 * Interface TestHistoryRepositoryInterface
 * @package Acme\Entity\TestHistory
 */
interface TestHistoryRepositoryInterface
{
    public function createSchema();

    /**
     * @return TestHistoryDTO
     */
    public function getLast(): TestHistoryDTO;

    /**
     * @param TestHistoryDTO $test
     */
    public function add(TestHistoryDTO $test);
}