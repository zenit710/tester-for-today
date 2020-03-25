<?php

namespace Command\Absence;

use Acme\Command\Absence\AbsenceList;
use Acme\Entity\Absence\AbsenceFilter;
use Acme\Entity\Absence\AbsenceRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AbsenceListTest extends TestCase
{
    /** @var AbsenceList */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(AbsenceRepository::class);
        $this->command = new AbsenceList($logger, $this->repo);
    }

    public function testRun()
    {
        $filter = new AbsenceFilter();
        $filter->setCanceled('1');
        $filter->setMemberId('2');
        $filter->setEndsFrom('2099-12-20');
        $filter->setEndsTo('2099-12-31');
        $filter->setStartsFrom('2099-01-20');
        $filter->setStartsTo('2099-01-31');

        $this->repo->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo($filter));

        $this->command->run([
            '--canceled=1',
            '--member-id=2',
            '--starts-from=2099-01-20',
            '--starts-to=2099-01-31',
            '--ends-from=2099-12-20',
            '--ends-to=2099-12-31',
        ]);
    }
}
