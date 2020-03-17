<?php

namespace Acme\Entity\Tester;

/**
 * Class TesterDTO
 * @package Acme\Entity\Tester
 */
class TesterDTO
{
    /** @var int */
    public $id;

    /** @var int */
    public $memberId;

    /** @var string */
    public $date;

    /**
     * TesterDTO constructor.
     */
    public function __construct()
    {
        $this->date = date('Y-m-d');
    }

    /**
     * @param array $arr
     * @return TesterDTO
     */
    public static function fromArray(array $arr): TesterDTO {
        $tester = new TesterDTO();

        if (!empty($arr['id'])) {
            $tester->id = $arr['id'];
        }
        if (!empty($arr['memberId'])) {
            $tester->memberId = $arr['memberId'];
        }
        if (!empty($arr['date'])) {
            $tester->date = $arr['date'];
        }

        return $tester;
    }
}