<?php

namespace Acme\Command\Subscriber;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Subscriber\SubscriberDTO;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;

/**
 * Class SubscriberAdd
 * @package Acme\Command\Subscriber
 */
class SubscriberAdd extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'New subscriber added!' . PHP_EOL;

    const ARG_EMAIL = 'email';
    const REQUIRED_ARGS = [
        self::ARG_EMAIL
    ];

    /** @var string */
    protected $commandName = 'subscriber:add';

    /** @var SubscriberRepositoryInterface */
    private $repository;

    /**
     * SubscriberAdd constructor.
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

        $subscriber = new SubscriberDTO();
        $subscriber->email = $this->commandArgs[self::ARG_EMAIL];

        $this->repository->add($subscriber);

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Add new subscriber' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --email=email - set name for added subscriber (required)" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}