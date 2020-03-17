<?php

namespace Acme\Entity\Subscriber;

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
     */
    public function getById(int $id): SubscriberDTO;

    /**
     * @param SubscriberDTO $subscriber
     */
    public function add(SubscriberDTO $subscriber);

    /**
     * @param int $id
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