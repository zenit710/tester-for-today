<?php

namespace Acme;

use Acme\Logger\Logger;

/**
 * Class TesterFileRepository
 * @package Acme
 */
class TesterFileRepository {
    const TEAM_FILE = 'team.txt';
    const TESTER_FILE = 'tester.txt';

    /** @var TesterFileRepository */
    private static $instance = null;

    /** @var Logger */
    private $logger;

    /** @var array */
    private $team;

    /** @var int */
    private $currentTesterId;

    /**
     * TesterFileRepository constructor.
     */
    private function __construct()
    {
        $this->logger = Logger::getInstance();
        $this->fetchTeam();
        $this->fetchLastTester();
    }

    /**
     * @return TesterFileRepository
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new TesterFileRepository();
        }

        return self::$instance;
    }

    public function getCurrent() {
        return $this->team[$this->currentTesterId];
    }

    public function getCurrentTesterId() {
        return $this->currentTesterId;
    }

    public function setCurrentTesterId($id) {
        if ($id > $this->getTeamSize()) {
            $this->logger->alert('Tester ' . $id . ' not exist!');
        }

        $this->currentTesterId = $id;
        $this->saveCurrentTesterId();
    }

    public function getNext() {
        return $this->team[$this->getNextTesterId()];
    }

    public function getNextTesterId() {
        return ($this->currentTesterId + 1) % $this->getTeamSize();
    }

    public function getTeamSize() {
        return count($this->team);
    }

    private function fetchTeam() {
        $teamFile = $this->readFile(self::TEAM_FILE);

        $team = explode("\n", $teamFile);
        array_splice($team, -1, 1);

        $this->team = $team;
    }

    private function fetchLastTester() {
        $testerContent = $this->readFile(self::TESTER_FILE);

        $this->currentTesterId = (int)$testerContent;
    }

    private function readFile($path) {
        if (!file_exists($path)) {
            $this->logger->alert($path . ' not exist!');
        }

        return file_get_contents(ROOTPATH . '/' . $path);
    }

    private function writeFile($path, $content) {
        if (!file_exists($path)) {
            $this->logger->alert($path . ' not exist!');
        }

        file_put_contents(ROOTPATH . '/' . $path, $content);
    }

    private function saveCurrentTesterId() {
        $this->writeFile(self::TESTER_FILE, $this->currentTesterId);
    }
}