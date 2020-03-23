<?php

namespace Acme\Command\Member;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Member\MemberRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class MemberStatusChange
 * @package Acme\Command\Member
 */
class MemberStatusChange extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Member status changed!' . PHP_EOL;

    const ARG_ID = 'id';
    const ARG_ACTIVE = 'active';
    const ARG_INACTIVE = 'inactive';
    const REQUIRED_ARGS = [
        self::ARG_ID,
    ];

    /** @var string */
    protected $commandName = 'member:status';

    /** @var MemberRepositoryInterface */
    private $repository;

    /**
     * MemberStatusChange constructor.
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

        if (!$this->validateArgs(self::REQUIRED_ARGS)) {
            throw new MissingArgumentException($this->help());
        }

        if ($this->hasHelpArg()) {
            return $this->help();
        }

        $memberId = $this->commandArgs[self::ARG_ID];

        if ($this->hasArg(self::ARG_ACTIVE)) {
            $this->repository->activate($memberId);
        } else if ($this->hasArg(self::ARG_INACTIVE)) {
            $this->repository->deactivate($memberId);
        } else {
            $member = $this->repository->getById($memberId);

            $member->active
                ? $this->repository->deactivate($memberId)
                : $this->repository->activate($memberId);
        }

        $this->logger->info('Member id: ' . $memberId . ' status changed');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Change member status' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --id=id - set id for member to change status (required)" . PHP_EOL
            . "\t --active - activate member" . PHP_EOL
            . "\t --inactive - deactivate member" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}