<?php

namespace Acme\Command\Tester;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Tester\TesterDTO;
use Acme\Entity\Tester\TesterRepositoryInterface;

/**
 * Class TesterAdd
 * @package Acme\Command\Tester
 */
class TesterAdd extends AbstractCommand
{
    const COMMAND_NAME = 'tester:add';
    const SUCCESS_MESSAGE = 'New tester added!';

    const ARG_NAME = 'name';
    const REQUIRED_ARGS = [
        self::ARG_NAME
    ];

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
    public function supports(string $name): bool
    {
        return self::COMMAND_NAME == $name;
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

        $tester = new TesterDTO();
        $tester->name = $this->commandArgs[self::ARG_NAME];

        $this->repository->add($tester);

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return self::COMMAND_NAME . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --name=name - set name for added tester (required for add)" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}