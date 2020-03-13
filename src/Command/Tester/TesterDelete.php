<?php

namespace Acme\Command\Tester;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Tester\TesterRepositoryInterface;

/**
 * Class TesterDelete
 * @package Acme\Command\Tester
 */
class TesterDelete extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Tester deleted!' . PHP_EOL;

    const ARG_ID = 'id';
    const REQUIRED_ARGS = [
        self::ARG_ID
    ];

    /** @var string */
    protected $commandName = 'tester:delete';

    /** @var TesterRepositoryInterface */
    private $repository;

    /**
     * TesterAdd constructor.
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

        if (!$this->validateArgs(self::REQUIRED_ARGS)) {
            throw new MissingArgumentException($this->help());
        }

        $this->repository->delete($this->commandArgs[self::ARG_ID]);

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --id=id - set id for tester to delete (required for delete)" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }

}