<?php

namespace Acme;

use Acme\Command\CommandBus;
use Acme\Command\Subscriber\SubscriberAdd;
use Acme\Command\Subscriber\SubscriberClear;
use Acme\Command\Subscriber\SubscriberDelete;
use Acme\Command\Subscriber\SubscriberList;
use Acme\Command\Subscriber\SubscriberStatusChange;
use Acme\Command\Tester\TesterSwitch;
use Acme\Command\Member\MemberAdd;
use Acme\Command\Member\MemberClear;
use Acme\Command\Member\MemberDelete;
use Acme\Command\Member\MemberList;
use Acme\Command\Member\MemberStatusChange;
use Acme\Command\Tester\TesterClear;
use Acme\Command\Tester\TesterCurrent;
use Acme\Entity\Subscriber\SubscriberRepository;
use Acme\Entity\Member\MemberRepository;
use Acme\Entity\Tester\TesterRepository;
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

        $this->registerService('MemberRepository', new MemberRepository($db));
        $this->registerService('SubscriberRepository', new SubscriberRepository($db));
        $this->registerService('TesterRepository', new TesterRepository($db));
    }

    private function bootstrapMailer()
    {
        $this->registerService('mail', new Mail());
    }

    private function bootstrapCommandBus()
    {
        $memberRepository = $this->getService('MemberRepository');
        $subscriberRepository = $this->getService('SubscriberRepository');
        $testerRepository = $this->getService('TesterRepository');
        $logger = $this->getService('logger');

        $commandBus = new CommandBus();

        // Member Commands
        $commandBus->register(new MemberAdd($logger, $memberRepository));
        $commandBus->register(new MemberList($logger, $memberRepository));
        $commandBus->register(new MemberDelete($logger, $memberRepository));
        $commandBus->register(new MemberStatusChange($logger, $memberRepository));
        $commandBus->register(new MemberClear($logger, $memberRepository));

        // Subscriber Commands
        $commandBus->register(new SubscriberAdd($logger, $subscriberRepository));
        $commandBus->register(new SubscriberList($logger, $subscriberRepository));
        $commandBus->register(new SubscriberDelete($logger, $subscriberRepository));
        $commandBus->register(new SubscriberStatusChange($logger, $subscriberRepository));
        $commandBus->register(new SubscriberClear($logger, $subscriberRepository));

        // Tester Commands
        $commandBus->register(new TesterCurrent($logger, $testerRepository));
        $commandBus->register(new TesterClear($logger, $testerRepository));
        $commandBus->register(new TesterSwitch($logger, $testerRepository, $memberRepository, $subscriberRepository));

        $this->commandBus = $commandBus;
    }
}