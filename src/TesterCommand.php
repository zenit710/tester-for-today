<?php

namespace Acme;

use Acme\Logger\Logger;

/**
 * Class TesterCommand
 * @package Acme
 */
class TesterCommand
{
    const NEXT_TESTER = 'next';
    const CHOOSE_TESTER = 'choose';
    const COMMANDS = [
        self::NEXT_TESTER,
        self::CHOOSE_TESTER
    ];

    /** @var Logger */
    private $logger;

    /** @var TesterFileRepository */
    private $testerRepository;

    /**
     * TesterCommand constructor.
     */
    public function __construct()
    {
        $this->logger = Logger::getInstance();
        $this->testerRepository = TesterFileRepository::getInstance();
    }

    public function run($command, $who = null) {
        if (!in_array($command, self::COMMANDS)) {
            $this->logger->alert('Unknown command!');
        }

        $newTester = null;

        switch ($command) {
            case self::NEXT_TESTER:
                $newTester = $this->chooseNextTester();
                break;
            case self::CHOOSE_TESTER:
                $newTester = $this->chooseTesterManually($who);
                break;
        }

        $this->logger->alert($newTester . ' is new tester!');
    }

    private function chooseNextTester() {
        $this->testerRepository->setCurrentTesterId(
            $this->testerRepository->getNextTesterId()
        );

        return $this->testerRepository->getCurrent();
    }

    private function chooseTesterManually($who) {
        $this->testerRepository->setCurrentTesterId($who);

        return $this->testerRepository->getCurrent();
    }
}