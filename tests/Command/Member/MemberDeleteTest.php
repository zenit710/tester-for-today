<?php

namespace Command\Member;

use Acme\Command\Member\MemberDelete;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Member\MemberRepository;
use Acme\Entity\NothingToDeleteException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MemberDeleteTest extends TestCase
{
    /** @var MemberDelete */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(MemberRepository::class);
        $this->command = new MemberDelete($logger, $this->repo);
    }

    public function testRunWithoutRequiredParams()
    {
        $this->expectException(MissingArgumentException::class);
        $this->command->run([]);
    }

    public function testRunWhenNoMember()
    {
        $this->expectException(NothingToDeleteException::class);

        $this->repo->expects($this->once())
            ->method('delete')
            ->willThrowException(new NothingToDeleteException());

        $this->command->run(['--id=1']);
    }

    public function testRun()
    {
        $this->repo->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1']);
    }
}
