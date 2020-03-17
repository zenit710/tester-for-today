<?php

namespace Acme\Entity\Subscriber;

use Acme\Entity\NoResultException;
use Acme\Entity\NothingToDeleteException;
use Acme\Entity\NothingToUpdateException;

/**
 * Interface SubscriberRepositoryInterface
 * @package Acme\Entity\Subscriber
 */
interface SubscriberRepositoryInterface
{
    public function createSchema();

    /**
     * @param SubscriberFilter $filter
     * @return SubscriberDTO[]
     */
    public function getAll(SubscriberFilter $filter = null): array;

    /**
     * @param int $id
     * @return SubscriberDTO
     * @throws NoResultException
     */
    public function getById(int $id): SubscriberDTO;

    /**
     * @param SubscriberDTO $subscriber
     */
    public function add(SubscriberDTO $subscriber);

    /**
     * @param int $id
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