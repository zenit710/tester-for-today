<?php

namespace Acme\Entity\TestHistory;

/**
 * Class TestHistoryDTO
 * @package Acme\Entity\TestHistory
 */
class TestHistoryDTO
{
    /** @var int */
    public $id;

    /** @var int */
    public $testerId;

    /** @var string */
    public $date;

    /**
     * TestHistoryDTO constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->date = (new \DateTime())->format('Y-m-d');
    }

    /**
     * @param array $arr
     * @return TestHistoryDTO
     */
    public static function fromArray(array $arr): TestHistoryDTO {
        $entry = new TestHistoryDTO();

        if (!empty($arr['id'])) {
            $entry->id = $arr['id'];
        }
        if (!empty($arr['testerId'])) {
            $entry->testerId = $arr['testerId'];
        }
        if (!empty($arr['date'])) {
            $entry->date = $arr['date'];
        }

        return $entry;
    }
}