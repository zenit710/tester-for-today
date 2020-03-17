<?php

namespace Acme\Command\Member;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\Member\MemberRepositoryInterface;

/**
 * Class MemberAdd
 * @package Acme\Command\Member
 */
class MemberAdd extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'New member added!' . PHP_EOL;

    const ARG_NAME = 'name';
    const REQUIRED_ARGS = [
        self::ARG_NAME
    ];

    /** @var string */
    protected $commandName = 'member:add';

    /** @var MemberRepositoryInterface */
    private $repository;

    /**
     * MemberAdd constructor.
     * @param MemberRepositoryInterface $repository
     */
    public function __construct(MemberRepositoryInterface $repository)
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

        if ($this->hasHelpArg()) {
            return $this->help();
        }

        $member = new MemberDTO();
        $member->name = $this->commandArgs[self::ARG_NAME];

        $this->repository->add($member);

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Add new member' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --name=name - set name for added member (required)" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}