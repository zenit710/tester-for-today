<?php

namespace Command\Absence;

use Acme\Command\Absence\AbsenceStatusChange;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Absence\AbsenceDTO;
use Acme\Entity\Absence\AbsenceRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AbsenceStatusChangeTest extends TestCase
{
    /** @var AbsenceStatusChange */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(AbsenceRepository::class);
        $this->command = new AbsenceStatusChange($logger, $this->repo);
    }

    public function testRunWithoutRequiredArgs()
    {
        $this->expectException(MissingArgumentException::class);
        $this->command->run([]);
    }

    public function testRunCancelByToggle()
    {
        $activeDto = new AbsenceDTO();
        $activeDto->canceled = false;

        $this->repo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'))
            ->willReturn($activeDto);

        $this->repo->expects($this->once())
            ->method('cancel')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1']);
    }

    public function testRunRestoreByToggle()
    {
        $canceledDto = new AbsenceDTO();
        $canceledDto->canceled = true;

        $this->repo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'))
            ->willReturn($canceledDto);

        $this->repo->expects($this->once())
            ->method('restore')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1']);
    }

    public function testRunRestoreByArg()
    {
        $this->repo->expects($this->once())
            ->method('restore')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1', '--restore']);
    }

    public function testRunCancelByArg()
    {
        $this->repo->expects($this->once())
            ->method('cancel')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1', '--cancel']);
    }
}
