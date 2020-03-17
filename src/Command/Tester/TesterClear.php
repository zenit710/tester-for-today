<?php

namespace Acme\Command\Tester;

use Acme\Command\AbstractCommand;
use Acme\Entity\Tester\TesterRepositoryInterface;

/**
 * Class TesterClear
 * @package Acme\Command\Tester
 */
class TesterClear extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Testers cleared!' . PHP_EOL;

    /** @var string */
    protected $commandName = 'tester:clear';

    /** @var TesterRepositoryInterface */
    private $repository;

    /**
     * TesterClear constructor.
     * @param TesterRepositoryInterface $repository
     */
    public function __construct(TesterRepositoryInterface $repository)
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
        return 'Clear testers' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}