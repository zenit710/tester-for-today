<?php

namespace Acme\Command\Tester;

use Acme\Command\AbstractCommand;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Absence\AbsenceFilter;
use Acme\Entity\Absence\AbsenceRepositoryInterface;
use Acme\Entity\NoResultException;
use Acme\Entity\Subscriber\SubscriberFilter;
use Acme\Entity\Subscriber\SubscriberRepositoryInterface;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\Member\MemberRepositoryInterface;
use Acme\Entity\Tester\TesterDTO;
use Acme\Entity\Tester\TesterRepositoryInterface;
use Acme\Service\Mail\MailServiceInterface;
use Psr\Log\LoggerInterface;

/**
 * Class SwitchTester
 * @package Acme\Command\TestHistory
 */
class TesterSwitch extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Tester changed!' . PHP_EOL;
    const MAIL_SUBJECT = 'Tester na dziś';
    const MESSAGE_PATTERN = 'Dzisiaj (%s) zadania testuje: %s';

    const ARG_MANUAL = 'manual';
    const ARG_AUTO = 'auto';
    const ARG_ID = 'id';

    /** @var string */
    protected $commandName = 'tester:switch';

    /** @var TesterRepositoryInterface */
    private $testerRepository;

    /** @var MemberRepositoryInterface */
    private $memberRepository;

    /** @var SubscriberRepositoryInterface */
    private $subscriberRepository;

    /** @var AbsenceRepositoryInterface */
    private $absenceRepository;

    /** @var MailServiceInterface */
    private $mailService;

    /**
     * TesterSwitch constructor.
     * @param LoggerInterface $logger
     * @param TesterRepositoryInterface $testerRepository
     * @param MemberRepositoryInterface $memberRepository
     * @param SubscriberRepositoryInterface $subscriberRepository
     * @param AbsenceRepositoryInterface $absenceRepository
     * @param MailServiceInterface $mailService
     */
    public function __construct(
        LoggerInterface $logger,
        TesterRepositoryInterface $testerRepository,
        MemberRepositoryInterface $memberRepository,
        SubscriberRepositoryInterface $subscriberRepository,
        AbsenceRepositoryInterface $absenceRepository,
        MailServiceInterface $mailService
    )
    {
        parent::__construct($logger);
        $this->testerRepository = $testerRepository;
        $this->memberRepository = $memberRepository;
        $this->subscriberRepository = $subscriberRepository;
        $this->absenceRepository = $absenceRepository;
        $this->mailService = $mailService;
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
            $nextTester = $this->memberRepository->getNextActiveById($id);
        } else if ($this->hasArg(self::ARG_MANUAL) && $this->hasArg(self::ARG_ID)) {
            $nextTester = $this->memberRepository->getById($this->getArg(self::ARG_ID));

            if (!$nextTester->active || $this->isMemberAbsent($nextTester->id)) {
                throw new InactiveTesterException('Selected member is not active!');
            }

            $this->logger->info('New tester selected manually');
        } else {
            throw new MissingArgumentException($this->help());
        }

        $tester->memberId = $nextTester->id;
        $this->testerRepository->add($tester);
        $this->logger->info('Tester changed. Current tester id: ' . $nextTester->id);
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
     * @param int $memberId
     * @return bool
     */
    private function isMemberAbsent(int $memberId): bool
    {
        $filter = new AbsenceFilter();
        $filter->setMemberId($memberId);
        $filter->setStartsTo(date('Y-m-d'));
        $filter->setEndsFrom(date('Y-m-d'));

        $memberAbsences = $this->absenceRepository->getAll($filter);

        return count($memberAbsences) > 0;
    }

    /**
     * @param MemberDTO $newTester
     */
    private function notifySubscribers(MemberDTO $newTester)
    {
        $subscriberFilter = new SubscriberFilter();
        $subscriberFilter->setActive(true);

        $subscribers = $this->subscriberRepository->getAll($subscriberFilter);

        if (count($subscribers) == 0) {
            return;
        }

        $message = sprintf(self::MESSAGE_PATTERN, date('d-m-Y'), $newTester->name);

        $mail = $this->mailService->create();
        $mail->Subject = self::MAIL_SUBJECT;
        $mail->Body = $message;

        foreach ($subscribers as $subscriber) {
            $mail->addBCC($subscriber->email);
        }

        if (!$mail->send()) {
            $this->logger->error('Mailer Error: ' . $mail->ErrorInfo);
        } else {
            $this->logger->debug('Message sent!');
        }
    }
}