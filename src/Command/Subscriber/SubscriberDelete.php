<?php

namespace Acme\Command\Subscriber;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SubscriberDelete
 * @package Acme\Command\Subscriber
 */
class SubscriberDelete extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Subscriber deleted!' . PHP_EOL;

    const ARG_ID = 'id';
    const REQUIRED_ARGS = [
        self::ARG_ID
    ];

    /** @var string */
    protected $commandName = 'subscriber:delete';

    /** @var SubscriberRepositoryInterface */
    private $repository;

    /**
     * SubscriberDelete constructor.
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

        $this->repository->delete($this->getArg(self::ARG_ID));
        $this->logger->info('Subscriber id: ' . $this->getArg(self::ARG_ID) . ' deleted');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Delete subscriber' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --id=id - set id for subscriber to delete (required)" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}