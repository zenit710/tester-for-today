<?php

namespace Entity\Tester;

use Acme\Entity\Tester\TesterDTO;
use PHPUnit\Framework\TestCase;

class TesterDTOTest extends TestCase
{
    public function testFromArray()
    {
        $testerData = [
            'id' => 1,
            'memberId' => 2,
            'date' => '2099-12-31'
        ];

        $tester = TesterDTO::fromArray($testerData);

        $this->assertSame($testerData['id'], $tester->id);
        $this->assertSame($testerData['memberId'], $tester->memberId);
        $this->assertSame($testerData['date'], $tester->date);
    }
}
