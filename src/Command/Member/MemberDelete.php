<?php

namespace Acme\Command\Member;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Member\MemberRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class MemberDelete
 * @package Acme\Command\Member
 */
class MemberDelete extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Member deleted!' . PHP_EOL;

    const ARG_ID = 'id';
    const REQUIRED_ARGS = [
        self::ARG_ID
    ];

    /** @var string */
    protected $commandName = 'member:delete';

    /** @var MemberRepositoryInterface */
    private $repository;

    /**
     * MemberDelete constructor.
     * @param LoggerInterface $logger
     * @param MemberRepositoryInterface $repository
     */
    public function __construct(LoggerInterface $logger, MemberRepositoryInterface $repository)
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

        $this->repository->delete($this->getArg(self::ARG_ID));
        $this->logger->info('Member id: ' . $this->getArg(self::ARG_ID) . ' deleted');

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Delete member' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --id=id - set id for member to delete (required)" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}