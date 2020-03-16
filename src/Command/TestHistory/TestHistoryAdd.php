<?php

namespace Acme\Command\TestHistory;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\NoResultException;
use Acme\Entity\Tester\TesterRepositoryInterface;
use Acme\Entity\TestHistory\TestHistoryDTO;
use Acme\Entity\TestHistory\TestHistoryRepositoryInterface;

/**
 * Class TestHistoryAdd
 * @package Acme\Command\TestHistory
 */
class TestHistoryAdd extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Tester added to history!' . PHP_EOL;

    const ARG_MANUAL = 'manual';
    const ARG_AUTO = 'auto';
    const ARG_ID = 'id';

    /** @var string */
    protected $commandName = 'test-history:add';

    /** @var TestHistoryRepositoryInterface */
    private $repository;

    /** @var TesterRepositoryInterface */
    private $testerRepository;

    /**
     * TestHistoryAdd constructor.
     * @param TestHistoryRepositoryInterface $repository
     * @param TesterRepositoryInterface $testerRepository
     */
    public function __construct(TestHistoryRepositoryInterface $repository, TesterRepositoryInterface $testerRepository)
    {
        $this->repository = $repository;
        $this->testerRepository = $testerRepository;
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

        $history = new TestHistoryDTO();

        if ($this->hasArg(self::ARG_AUTO)) {
            $id = $this->getLastTesterId();
            $nextTester = $this->testerRepository->getNextById($id);

            $history->testerId = $nextTester->id;
        } else if ($this->hasArg(self::ARG_MANUAL) && $this->hasArg(self::ARG_ID)) {
            $history->testerId = $this->getArg(self::ARG_ID);
        } else {
            throw new MissingArgumentException($this->help());
        }

        $this->repository->add($history);

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Get current tester' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --auto - add next tester automatically" . PHP_EOL
            . "\t --manual - add next tester manually" . PHP_EOL
            . "\t\t --id - tester id to add manually" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }

    /**
     * @return int
     */
    private function getLastTesterId(): int
    {
        try {
            $tester = $this->repository->getLastTester();

            return $tester->id;
        } catch (NoResultException $e) {
            return 0;
        }
    }
}