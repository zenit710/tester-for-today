<?php

namespace Command\Member;

use Acme\Command\Member\MemberAdd;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\Member\MemberRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MemberAddTest extends TestCase
{
    /** @var MemberAdd */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(MemberRepository::class);
        $this->command = new MemberAdd($logger, $this->repo);
    }

    public function testRunWithoutRequiredParams()
    {
        $this->expectException(MissingArgumentException::class);
        $this->command->run([]);
    }

    public function testRunWithRequiredParams()
    {
        $memberDTO = new MemberDTO();
        $memberDTO->name = 'tester';

        $this->repo->expects($this->once())
            ->method('add')
            ->with($this->equalTo($memberDTO));
        $this->command->run(['--name=tester']);
    }
}
