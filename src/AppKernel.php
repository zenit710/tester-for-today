<?php

namespace Acme;

use Acme\Command\CommandBus;
use Acme\Command\Subscriber\SubscriberAdd;
use Acme\Command\Subscriber\SubscriberClear;
use Acme\Command\Subscriber\SubscriberDelete;
use Acme\Command\Subscriber\SubscriberList;
use Acme\Command\Subscriber\SubscriberStatusChange;
use Acme\Command\Tester\TesterAdd;
use Acme\Command\Tester\TesterClear;
use Acme\Command\Tester\TesterDelete;
use Acme\Command\Tester\TesterList;
use Acme\Command\Tester\TesterStatusChange;
use Acme\Command\TestHistory\TestHistoryCurrent;
use Acme\Entity\Subscriber\SubscriberRepository;
use Acme\Entity\Tester\TesterRepository;
use Acme\Entity\TestHistory\TestHistoryRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Monolog;

/**
 * Class AppKernel
 * @package Acme
 */
class AppKernel
{
    /** @var AppKernel */
    private static $instance = null;

    /** @var array */
    private $services = [];

    /** @var CommandBus */
    private $commandBus;

    private function __construct() {}

    private function __clone() {}

    /**
     * @return AppKernel
     */
    public static function getInstance(): AppKernel
    {
        if (is_null(self::$instance)) {
            self::$instance = new AppKernel();
        }

        return self::$instance;
    }

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
        $subscriberRepository = $this->getService('SubscriberRepository');
        $historyRepository = $this->getService('TestHistoryRepository');

        $commandBus = new CommandBus();

        // Tester Commands
        $commandBus->register(new TesterAdd($testerRepository));
        $commandBus->register(new TesterList($testerRepository));
        $commandBus->register(new TesterDelete($testerRepository));
        $commandBus->register(new TesterStatusChange($testerRepository));
        $commandBus->register(new TesterClear($testerRepository));

        // SubscriberCommands
        $commandBus->register(new SubscriberAdd($subscriberRepository));
        $commandBus->register(new SubscriberList($subscriberRepository));
        $commandBus->register(new SubscriberDelete($subscriberRepository));
        $commandBus->register(new SubscriberStatusChange($subscriberRepository));
        $commandBus->register(new SubscriberClear($subscriberRepository));

        // TestHistoryCommands
        $commandBus->register(new TestHistoryCurrent($historyRepository));

        $this->commandBus = $commandBus;
    }
}