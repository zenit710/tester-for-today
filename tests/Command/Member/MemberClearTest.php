<?php

namespace Command\Member;

use Acme\Command\Member\MemberClear;
use Acme\Entity\Member\MemberRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MemberClearTest extends TestCase
{
    /** @var MemberClear */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(MemberRepository::class);
        $this->command = new MemberClear($logger, $this->repo);
    }

    public function testRun()
    {
        $this->repo->expects($this->once())
            ->method('clear');
        $this->command->run([]);
    }
}
