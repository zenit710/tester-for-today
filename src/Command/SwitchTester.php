<?php

namespace Acme\Command;

use Acme\Entity\NoResultException;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;
use Acme\Entity\Tester\TesterDTO;
use Acme\Entity\Tester\TesterRepositoryInterface;
use Acme\Entity\TestHistory\TestHistoryDTO;
use Acme\Entity\TestHistory\TestHistoryRepositoryInterface;
use Acme\Mail;

/**
 * Class SwitchTester
 * @package Acme\Command\TestHistory
 */
class SwitchTester extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Tester added to history!' . PHP_EOL;
    const MAIL_SUBJECT = 'Tester na dziś';
    const MESSAGE_PATTERN = 'Dzisiaj (%s) zadania testuje: %s';

    const ARG_MANUAL = 'manual';
    const ARG_AUTO = 'auto';
    const ARG_ID = 'id';

    /** @var string */
    protected $commandName = 'switch:tester';

    /** @var TestHistoryRepositoryInterface */
    private $repository;

    /** @var TesterRepositoryInterface */
    private $testerRepository;

    /** @var SubscriberRepositoryInterface */
    private $subscriberRepository;

    /**
     * SwitchTester constructor.
     * @param TestHistoryRepositoryInterface $repository
     * @param TesterRepositoryInterface $testerRepository
     * @param SubscriberRepositoryInterface $subscriberRepository
     */
    public function __construct(
        TestHistoryRepositoryInterface $repository,
        TesterRepositoryInterface $testerRepository,
        SubscriberRepositoryInterface $subscriberRepository
    )
    {
        $this->repository = $repository;
        $this->testerRepository = $testerRepository;
        $this->subscriberRepository = $subscriberRepository;
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
        } else if ($this->hasArg(self::ARG_MANUAL) && $this->hasArg(self::ARG_ID)) {
            $nextTester = $this->testerRepository->getById($this->getArg(self::ARG_ID));
        } else {
            throw new MissingArgumentException($this->help());
        }

        $history->testerId = $nextTester->id;
        $this->repository->add($history);
        $this->notifySubscribers($nextTester);

        return self::SUCCESS_MESSAGE;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Switch current tester' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --auto - switch tester automatically" . PHP_EOL
            . "\t --manual - switch tester manually" . PHP_EOL
            . "\t\t --id - tester id to set as current" . PHP_EOL
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

    /**
     * @param TesterDTO $newTester
     */
    private function notifySubscribers(TesterDTO $newTester)
    {
        $subscribers = $this->subscriberRepository->getAll();
        $message = sprintf(self::MESSAGE_PATTERN, date('d-m-Y'), $newTester->name);

        $mail = new Mail();
        $mail->Subject = self::MAIL_SUBJECT;
        $mail->Body = $message;

        foreach ($subscribers as $subscriber) {
            $mail->addBCC($subscriber->email);
        }

        var_dump($mail->send());
    }
}