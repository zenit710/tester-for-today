<?php

namespace Acme;

use Acme\Command\CommandBus;
use Acme\Command\Tester\TesterAdd;
use Acme\Entity\Subscriber\SubscriberRepository;
use Acme\Entity\Tester\TesterRepository;
use Acme\Entity\TestHistory\TestHistoryRepository;

/**
 * Class Container
 * @package Acme
 */
class Container
{
    /** @var array */
    private $services = [];

    /** @var CommandBus */
    private $commandBus;

    public function bootstrap()
    {
        $this->bootstrapRepositories();
        $this->bootstrapCommandBus();
    }

    public function bootstrapRepositories() {
        $this->registerService('TesterRepository', TesterRepository::getInstance());
        $this->registerService('SubscriberRepository', SubscriberRepository::getInstance());
        $this->registerService('TestHistoryRepository', TestHistoryRepository::getInstance());
    }

    public function bootstrapCommandBus()
    {
        $testerRepository = $this->getService('TesterRepository');

        $commandBus = new CommandBus();
        $commandBus->register(new TesterAdd($testerRepository));

        $this->commandBus = $commandBus;
    }

    /**
     * @param string $name
     * @param mixed $service
     */
    public function registerService(string $name, $service)
    {
        $this->services[$name] = $service;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getService(string $name)
    {
        return array_key_exists($name, $this->services) ? $this->services[$name] : null;
    }

    /**
     * @param string $command
     * @param array $args
     */
    public function handle(string $command, array $args)
    {
        echo $this->commandBus->handle($command, $args);
    }
}