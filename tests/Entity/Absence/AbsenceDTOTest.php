<?php

namespace Entity\Absence;

use Acme\Entity\Absence\AbsenceDTO;
use PHPUnit\Framework\TestCase;

class AbsenceDTOTest extends TestCase
{
    public function testFromArray()
    {
        $absenceData = [
            'id' => 1,
            'member_id' => 2,
            'date_from' => '2099-12-20',
            'date_to' => '2099-12-31',
            'canceled' => 0
        ];

        $absence = AbsenceDTO::fromArray($absenceData);

        $this->assertSame($absenceData['id'], $absence->id);
        $this->assertSame($absenceData['member_id'], $absence->memberId);
        $this->assertSame($absenceData['date_from'], $absence->dateFrom);
        $this->assertSame($absenceData['date_to'], $absence->dateTo);
        $this->assertFalse($absence->canceled);
    }
}
