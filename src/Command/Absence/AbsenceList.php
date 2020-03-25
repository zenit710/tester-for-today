<?php

namespace Acme\Command\Absence;

use Acme\Command\AbstractCommand;
use Acme\Entity\Absence\AbsenceDTO;
use Acme\Entity\Absence\AbsenceFilter;
use Acme\Entity\Absence\AbsenceRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class MemberList
 * @package Acme\Command\Member
 */
class AbsenceList extends AbstractCommand
{
    const NO_ABSENCES_MESSAGE = 'There are no absences!' . PHP_EOL;
    const ARG_CANCELED = 'canceled';
    const ARG_STARTS_FROM = 'starts-from';
    const ARG_STARTS_TO = 'starts-to';
    const ARG_ENDS_FROM = 'ends-from';
    const ARG_ENDS_TO = 'ends-to';
    const ARG_MEMBER = 'member-id';

    /** @var string */
    protected $commandName = 'absence:list';

    /** @var AbsenceRepositoryInterface */
    private $repository;

    /**
     * AbsenceList constructor.
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

        $filter = new AbsenceFilter();

        if ($this->hasArg(self::ARG_CANCELED)) {
            $filter->setCanceled($this->getArg(self::ARG_CANCELED));
        }
        if ($this->hasArg(self::ARG_STARTS_FROM)) {
            $filter->setStartsFrom($this->getArg(self::ARG_STARTS_FROM));
        }
        if ($this->hasArg(self::ARG_STARTS_TO)) {
            $filter->setStartsTo($this->getArg(self::ARG_STARTS_TO));
        }
        if ($this->hasArg(self::ARG_ENDS_FROM)) {
            $filter->setEndsFrom($this->getArg(self::ARG_ENDS_FROM));
        }
        if ($this->hasArg(self::ARG_ENDS_TO)) {
            $filter->setEndsTo($this->getArg(self::ARG_ENDS_TO));
        }
        if ($this->hasArg(self::ARG_MEMBER)) {
            $filter->setMemberId($this->getArg(self::ARG_MEMBER));
        }

        $absences = $this->repository->getAll($filter);

        return $this->castAbsencesArrayToString($absences);
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Print absences list' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --canceled=[0|1] - only canceled/keep absences" . PHP_EOL
            . "\t --starts-from=2099-12-31 - starts after date" . PHP_EOL
            . "\t --starts-to=2099-12-31 - starts before date" . PHP_EOL
            . "\t --ends-from=2099-12-31 - ends after date" . PHP_EOL
            . "\t --ends-from=2099-12-31 - ends before date" . PHP_EOL
            . "\t --member-id=id - only one member absences" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }

    /**
     * @param AbsenceDTO[] $absences
     * @return string
     */
    private function castAbsencesArrayToString(array $absences): string
    {
        if (count($absences) === 0) {
            return self::NO_ABSENCES_MESSAGE;
        }

        $columnsSizes = [
            'id' => 2,
            'member_id' => 9,
            'date_from' => 10,
            'date_to' => 10,
            'status' => 8,
        ];

        foreach ($absences as $absence) {
            $idLength = strlen(strval($absence->id));

            if ($idLength > $columnsSizes['id']) {
                $columnsSizes['id'] = $idLength;
            }
        }

        $outputArray = [
            [
                str_pad('id', $columnsSizes['id']),
                str_pad('member_id', $columnsSizes['member_id']),
                str_pad('date_from', $columnsSizes['date_from']),
                str_pad('date_to', $columnsSizes['date_to']),
                str_pad('status', $columnsSizes['status'])
            ]
        ];

        foreach ($absences as $absence) {
            $outputArray[] = [
                str_pad($absence->id, $columnsSizes['id']),
                str_pad($absence->memberId, $columnsSizes['member_id']),
                str_pad($absence->dateFrom, $columnsSizes['date_from']),
                str_pad($absence->dateTo, $columnsSizes['date_to']),
                str_pad($absence->canceled ? 'canceled' : 'ok', $columnsSizes['status'])
            ];
        }

        $output = '';

        foreach ($outputArray as $entry) {
            $output .= join(" | ", $entry) . PHP_EOL;
        }

        return $output;
    }
}