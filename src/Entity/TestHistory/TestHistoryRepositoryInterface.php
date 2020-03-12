<?php

namespace Acme\Entity\TestHistory;

use Acme\Entity\Tester\TesterDTO;

/**
 * Interface TestHistoryRepositoryInterface
 * @package Acme\Entity\TestHistory
 */
interface TestHistoryRepositoryInterface
{
    /**
     * @return TestHistoryRepositoryInterface
     */
    public static function getInstance(): TestHistoryRepositoryInterface;

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