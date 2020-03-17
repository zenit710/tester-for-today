<?php

namespace Acme\Command;

use Acme\Entity\NoResultException;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\Member\MemberRepositoryInterface;
use Acme\Entity\Tester\TesterDTO;
use Acme\Entity\Tester\TesterRepositoryInterface;
use Acme\Mail;

/**
 * Class SwitchTester
 * @package Acme\Command\TestHistory
 */
class SwitchTester extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Tester changed!' . PHP_EOL;
    const MAIL_SUBJECT = 'Tester na dziś';
    const MESSAGE_PATTERN = 'Dzisiaj (%s) zadania testuje: %s';

    const ARG_MANUAL = 'manual';
    const ARG_AUTO = 'auto';
    const ARG_ID = 'id';

    /** @var string */
    protected $commandName = 'switch:tester';

    /** @var TesterRepositoryInterface */
    private $testerRepository;

    /** @var MemberRepositoryInterface */
    private $memberRepository;

    /** @var SubscriberRepositoryInterface */
    private $subscriberRepository;

    /**
     * SwitchTester constructor.
     * @param TesterRepositoryInterface $testerRepository
     * @param MemberRepositoryInterface $memberRepository
     * @param SubscriberRepositoryInterface $subscriberRepository
     */
    public function __construct(
        TesterRepositoryInterface $testerRepository,
        MemberRepositoryInterface $memberRepository,
        SubscriberRepositoryInterface $subscriberRepository
    )
    {
        $this->testerRepository = $testerRepository;
        $this->memberRepository = $memberRepository;
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

        $tester = new TesterDTO();

        if ($this->hasArg(self::ARG_AUTO)) {
            $id = $this->getLastTesterId();
            $nextTester = $this->memberRepository->getNextById($id);
        } else if ($this->hasArg(self::ARG_MANUAL) && $this->hasArg(self::ARG_ID)) {
            $nextTester = $this->memberRepository->getById($this->getArg(self::ARG_ID));
        } else {
            throw new MissingArgumentException($this->help());
        }

        $tester->memberId = $nextTester->id;
        $this->testerRepository->add($tester);
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
            . "\t\t --id - member id to set as current" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }

    /**
     * @return int
     */
    private function getLastTesterId(): int
    {
        try {
            $tester = $this->testerRepository->getLastTester();

            return $tester->id;
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * @param MemberDTO $newTester
     */
    private function notifySubscribers(MemberDTO $newTester)
    {
        $subscribers = $this->subscriberRepository->getAll();
        $message = sprintf(self::MESSAGE_PATTERN, date('d-m-Y'), $newTester->name);

        $mail = new Mail();
        $mail->Subject = self::MAIL_SUBJECT;
        $mail->Body = $message;

        foreach ($subscribers as $subscriber) {
            $mail->addBCC($subscriber->email);
        }

        $mail->send();
    }
}