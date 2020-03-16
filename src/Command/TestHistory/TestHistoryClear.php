<?php

namespace Acme\Command\TestHistory;

use Acme\Command\AbstractCommand;
use Acme\Entity\TestHistory\TestHistoryRepositoryInterface;

/**
 * Class TestHistoryClear
 * @package Acme\Command\TestHistory
 */
class TestHistoryClear extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Test history cleared!' . PHP_EOL;

    /** @var string */
    protected $commandName = 'test-history:clear';

    /** @var TestHistoryRepositoryInterface */
    private $repository;

    /**
     * TestHistoryClear constructor.
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

        $this->repository->clear();

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
            . "\t --help - get help" . PHP_EOL;
    }
}