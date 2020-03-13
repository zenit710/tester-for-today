<?php

namespace Acme\Command\Subscriber;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;

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
     * @param SubscriberRepositoryInterface $repository
     */
    public function __construct(SubscriberRepositoryInterface $repository)
    {
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

        if (array_key_exists(self::ARG_ACTIVE, $this->commandArgs)) {
            $this->repository->activate($this->commandArgs[self::ARG_ID]);
        } else if (array_key_exists(self::ARG_INACTIVE, $this->commandArgs)) {
            $this->repository->deactivate($this->commandArgs[self::ARG_ID]);
        } else {
            $subscriber = $this->repository->getById($this->commandArgs[self::ARG_ID]);

            $subscriber->active
                ? $this->repository->deactivate($this->commandArgs[self::ARG_ID])
                : $this->repository->activate($this->commandArgs[self::ARG_ID]);
        }

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