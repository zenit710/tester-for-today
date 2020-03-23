<?php

namespace Acme\Command\Absence;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Absence\AbsenceDTO;
use Acme\Entity\Absence\AbsenceRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class AbsenceAdd
 * @package Acme\Command\Absence
 */
class AbsenceAdd extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'New absence added!' . PHP_EOL;

    const ARG_MEMBER_ID = 'member-id';
    const ARG_FROM = 'from';
    const ARG_TO = 'to';
    const REQUIRED_ARGS = [
        self::ARG_MEMBER_ID,
        self::ARG_FROM,
        self::ARG_TO,
    ];

    /** @var string */
    protected $commandName = 'absence:add';

    /** @var AbsenceRepositoryInterface */
    private $repository;

    /**
     * AbsenceAdd constructor.
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

        $absence = new AbsenceDTO();
        $absence->memberId = $this->getArg(self::ARG_MEMBER_ID);
        $absence->dateFrom = $this->getArg(self::ARG_FROM);
        $absence->dateTo = $this->getArg(self::ARG_TO);

        $this->repository->add($absence);
        $this->logger->info('New absence added for member: ' . $absence->memberId);

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Add new absence' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --member-id=id - absent member id (required)" . PHP_EOL
            . "\t --from=2099-12-24 - date from absent (required)" . PHP_EOL
            . "\t --to=2099-12-31 - date to absent (required)" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}