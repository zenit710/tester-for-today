<?php

namespace Acme\Entity\Tester;

/**
 * Interface TesterRepositoryInterface
 * @package Acme\Entity\Tester
 */
interface TesterRepositoryInterface
{
    public function createSchema();

    /**
     * @return TesterDTO[]
     */
    public function getAll(): array;

    /**
     * @param int $id
     * @return TesterDTO
     */
    public function getById(int $id): TesterDTO;

    /**
     * @param int $id
     * @return TesterDTO
     */
    public function getNextById(int $id): TesterDTO;

    /**
     * @param TesterDTO $tester
     */
    public function add(TesterDTO $tester);

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