<?php

namespace Acme\Command\Member;

use Acme\Command\AbstractCommand;
use Acme\Entity\Member\MemberRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class MemberClear
 * @package Acme\Command\Member
 */
class MemberClear extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'All members removed!' . PHP_EOL;

    /** @var string */
    protected $commandName = 'member:clear';

    /** @var MemberRepositoryInterface */
    private $repository;

    /**
     * MemberClear constructor.
     * @param LoggerInterface $logger
     * @param MemberRepositoryInterface $repository
     */
    public function __construct(LoggerInterface $logger, MemberRepositoryInterface $repository)
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
        $this->logger->info('Members list cleared');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Clear members' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}