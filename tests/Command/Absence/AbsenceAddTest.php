<?php

namespace Command\Absence;

use Acme\Command\Absence\AbsenceAdd;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Absence\AbsenceDTO;
use Acme\Entity\Absence\AbsenceRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AbsenceAddTest extends TestCase
{
    /** @var AbsenceAdd */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(AbsenceRepository::class);
        $this->command = new AbsenceAdd($logger, $this->repo);
    }

    public function testRunWithoutRequiredParams()
    {
        $this->expectException(MissingArgumentException::class);
        $this->command->run(['--member-id=1', '--from=2099-12-20']);
    }

    public function testRunWithRequiredParams()
    {
        $absenceDTO = new AbsenceDTO();
        $absenceDTO->memberId = '1';
        $absenceDTO->dateFrom = '2099-12-20';
        $absenceDTO->dateTo = '2099-12-31';

        $this->repo->expects($this->once())
            ->method('add')
            ->with($this->equalTo($absenceDTO));
        $this->command->run(['--member-id=1', '--from=2099-12-20', '--to=2099-12-31']);
    }
}
