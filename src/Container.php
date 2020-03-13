<?php

namespace Acme;

use Acme\Command\CommandBus;
use Acme\Command\Tester\TesterAdd;
use Acme\Command\Tester\TesterList;
use Acme\Entity\Subscriber\SubscriberRepository;
use Acme\Entity\Tester\TesterRepository;
use Acme\Entity\TestHistory\TestHistoryRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monolog;

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
        $this->bootstrapLogger();
        $this->bootstrapDb();
        $this->bootstrapRepositories();
        $this->bootstrapMailer();
        $this->bootstrapCommandBus();
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

    private function bootstrapLogger()
    {
        $logger = new Monolog('log');
        $logger->pushHandler(new StreamHandler(ROOTPATH . '/.data/_logs/app.log'));

        $this->registerService('logger', $logger);
    }

    private function bootstrapDb()
    {
        $this->registerService('db', new DbConnection());
    }

    private function bootstrapRepositories()
    {
        $db = $this->getService('db');

        $this->registerService('TesterRepository', new TesterRepository($db));
        $this->registerService('SubscriberRepository', new SubscriberRepository($db));
        $this->registerService('TestHistoryRepository', new TestHistoryRepository($db));
    }

    private function bootstrapMailer()
    {
        $this->registerService('mail', new Mail());
    }

    private function bootstrapCommandBus()
    {
        $testerRepository = $this->getService('TesterRepository');

        $commandBus = new CommandBus();
        $commandBus->register(new TesterAdd($testerRepository));
        $commandBus->register(new TesterList($testerRepository));

        $this->commandBus = $commandBus;
    }
}