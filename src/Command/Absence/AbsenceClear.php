<?php

namespace Acme\Command\Absence;

use Acme\Command\AbstractCommand;
use Acme\Entity\Absence\AbsenceRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class MemberClear
 * @package Acme\Command\Member
 */
class AbsenceClear extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'All absences removed!' . PHP_EOL;

    /** @var string */
    protected $commandName = 'absence:clear';

    /** @var AbsenceRepositoryInterface */
    private $repository;

    /**
     * AbsenceClear constructor.
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

        if ($this->hasHelpArg()) {
            return $this->help();
        }

        $this->repository->clear();
        $this->logger->info('Absence list cleared');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Clear absences' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}