<?php

namespace Command\Member;

use Acme\Command\Member\MemberList;
use Acme\Entity\Member\MemberFilter;
use Acme\Entity\Member\MemberRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MemberListTest extends TestCase
{
    /** @var MemberList */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(MemberRepository::class);
        $this->command = new MemberList($logger, $this->repo);
    }

    public function testRun()
    {
        $filter = new MemberFilter();
        $filter->setActive('0');

        $this->repo->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo($filter));

        $this->command->run(['--active=0']);
    }

    public function testRunWhenEmptyList()
    {
        $this->repo->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $this->assertSame(MemberList::NO_MEMBERS_MESSAGE, $this->command->run([]));
    }
}
