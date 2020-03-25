<?php

namespace Command\Absence;

use Acme\Command\Absence\AbsenceClear;
use Acme\Entity\Absence\AbsenceRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AbsenceClearTest extends TestCase
{
    /** @var AbsenceClear */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(AbsenceRepository::class);
        $this->command = new AbsenceClear($logger, $this->repo);
    }

    public function testRun()
    {
        $this->repo->expects($this->once())
            ->method('clear');
        $this->command->run([]);
    }
}
