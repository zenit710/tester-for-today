<?php

namespace Acme\Command\Subscriber;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SubscriberStatusChange
 * @package Acme\Command\Subscriber
 */
class SubscriberStatusChange extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Subscriber status changed!' . PHP_EOL;

    const ARG_ID = 'id';
    const ARG_ACTIVE = 'active';
    const ARG_INACTIVE = 'inactive';
    const REQUIRED_ARGS = [
        self::ARG_ID,
    ];

    /** @var string */
    protected $commandName = 'subscriber:status';

    /** @var SubscriberRepositoryInterface */
    private $repository;

    /**
     * SubscriberStatusChange constructor.
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

        if (!$this->validateArgs(self::REQUIRED_ARGS)) {
            throw new MissingArgumentException($this->help());
        }

        if ($this->hasHelpArg()) {
            return $this->help();
        }

        $subscriptionId = $this->commandArgs[self::ARG_ID];

        if ($this->hasArg(self::ARG_ACTIVE)) {
            $this->repository->activate($subscriptionId);
        } else if ($this->hasArg(self::ARG_INACTIVE)) {
            $this->repository->deactivate($subscriptionId);
        } else {
            $subscriber = $this->repository->getById($subscriptionId);

            $subscriber->active
                ? $this->repository->deactivate($subscriptionId)
                : $this->repository->activate($subscriptionId);
        }

        $this->logger->info('Subscriber id: ' . $subscriptionId . ' status changed');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Change subscriber status' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --id=id - set id for subscriber to change status (required)" . PHP_EOL
            . "\t --active - activate user" . PHP_EOL
            . "\t --inactive - deactivate user" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}