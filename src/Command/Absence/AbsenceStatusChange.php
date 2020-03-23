<?php

namespace Acme\Command\Absence;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Absence\AbsenceRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class MemberStatusChange
 * @package Acme\Command\Member
 */
class AbsenceStatusChange extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Absence status changed!' . PHP_EOL;

    const ARG_ID = 'id';
    const ARG_CANCEL = 'cancel';
    const ARG_RESTORE = 'restore';
    const REQUIRED_ARGS = [
        self::ARG_ID,
    ];

    /** @var string */
    protected $commandName = 'absence:status';

    /** @var AbsenceRepositoryInterface */
    private $repository;

    /**
     * AbsenceStatusChange constructor.
     * @param LoggerInterface $logger
     * @param AbsenceRepositoryInterface $repository
     */
    public function __construct(LoggerInterface $logger, AbsenceRepositoryInterface $repository)
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

        $absenceId = $this->getArg(self::ARG_ID);

        if ($this->hasArg(self::ARG_CANCEL)) {
            $this->repository->cancel($absenceId);
        } else if ($this->hasArg(self::ARG_RESTORE)) {
            $this->repository->restore($absenceId);
        } else {
            $absence = $this->repository->getById($absenceId);

            $absence->canceled
                ? $this->repository->restore($absenceId)
                : $this->repository->cancel($absenceId);
        }

        $this->logger->info('Absence id: ' . $absenceId . ' status changed');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Change absence status' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --id=id - member id to change status (required)" . PHP_EOL
            . "\t --cancel - cancel absence" . PHP_EOL
            . "\t --restore - restore canceled absence" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}