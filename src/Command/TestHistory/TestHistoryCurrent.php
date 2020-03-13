<?php

namespace Acme\Command\TestHistory;

use Acme\Command\AbstractCommand;
use Acme\Entity\NoResultException;
use Acme\Entity\TestHistory\TestHistoryRepositoryInterface;

/**
 * Class TestHistoryCurrent
 * @package Acme\Command\TestHistory
 */
class TestHistoryCurrent extends AbstractCommand
{
    const NO_TESTER_MESSAGE = 'There is no test history!' . PHP_EOL;
    const TESTER_MESSAGE_PATTERN = 'Current tester is: %s (id: %u)' . PHP_EOL;

    /** @var string */
    protected $commandName = 'test-history:current';

    /** @var TestHistoryRepositoryInterface */
    private $repository;

    /**
     * TestHistoryCurrent constructor.
     * @param TestHistoryRepositoryInterface $repository
     */
    public function __construct(TestHistoryRepositoryInterface $repository)
    {
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

        try {
            $tester = $this->repository->getLastTester();

            return sprintf(self::TESTER_MESSAGE_PATTERN, $tester->name, $tester->id);
        } catch (NoResultException $e) {
            return self::NO_TESTER_MESSAGE;
        }
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
            . "\t --help - get help" . PHP_EOL;
    }
}