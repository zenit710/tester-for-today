<?php

namespace Acme\Command\Subscriber;

use Acme\Command\AbstractCommand;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SubscriberClear
 * @package Acme\Command\Subscriber
 */
class SubscriberClear extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'All subscribers removed!' . PHP_EOL;

    /** @var string */
    protected $commandName = 'subscriber:clear';

    /** @var SubscriberRepositoryInterface */
    private $repository;

    /**
     * SubscriberClear constructor.
     * @param LoggerInterface $logger
     * @param SubscriberRepositoryInterface $repository
     */
    public function __construct(LoggerInterface $logger, SubscriberRepositoryInterface $repository)
    {
        parent::__construct($logger);
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    public function run(array $args): string
    {
        $this->mapArgs($args);

        if ($this->hasHelpArg()) {
            return $this->help();
        }

        $this->repository->clear();
        $this->logger->info('Subscriber list cleared');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Clear subscribers' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}