<?php

namespace Acme\Entity\Absence;

/**
 * Class AbsenceDTO
 * @package Acme\Entity\Absence
 */
class AbsenceDTO
{
    /** @var int */
    public $id;

    /** @var int */
    public $memberId;

    /** @var string */
    public $dateFrom;

    /** @var string */
    public $dateTo;

    /** @var boolean */
    public $canceled;

    /**
     * AbsenceDTO constructor.
     */
    public function __construct()
    {
        $this->dateFrom = date('Y-m-d');
    }

    /**
     * @param array $array
     * @return AbsenceDTO
     */
    public static function fromArray(array $array): AbsenceDTO
    {
        $absence = new AbsenceDTO();

        if (isset($array['id'])) {
            $absence->id = $array['id'];
        }
        if (isset($array['member_id'])) {
            $absence->memberId = $array['member_id'];
        }
        if (isset($array['date_from'])) {
            $absence->dateFrom = $array['date_from'];
        }
        if (isset($array['date_to'])) {
            $absence->dateTo = $array['date_to'];
        }
        if (isset($array['canceled'])) {
            $absence->canceled = !!$array['canceled'];
        }

        return $absence;
    }
}