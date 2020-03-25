<?php

namespace Command\Member;

use Acme\Command\Member\MemberStatusChange;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\Member\MemberRepository;
use Acme\Entity\NoResultException;
use Acme\Entity\NothingToUpdateException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MemberStatusChangeTest extends TestCase
{
    /** @var MemberStatusChange */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(MemberRepository::class);
        $this->command = new MemberStatusChange($logger, $this->repo);
    }

    public function testRunWithoutRequiredArgs()
    {
        $this->expectException(MissingArgumentException::class);
        $this->command->run([]);
    }

    public function testRunActivateByToggle()
    {
        $activeDto = new MemberDTO();
        $activeDto->active = false;

        $this->repo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'))
            ->willReturn($activeDto);

        $this->repo->expects($this->once())
            ->method('activate')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1']);
    }

    public function testRunDeactivateByToggle()
    {
        $inactiveDTO = new MemberDTO();
        $inactiveDTO->active = true;

        $this->repo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'))
            ->willReturn($inactiveDTO);

        $this->repo->expects($this->once())
            ->method('deactivate')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1']);
    }

    public function testRunActivateByArg()
    {
        $this->repo->expects($this->once())
            ->method('activate')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1', '--active']);
    }

    public function testRunDeactivateByArg()
    {
        $this->repo->expects($this->once())
            ->method('deactivate')
            ->with($this->equalTo('1'));

        $this->command->run(['--id=1', '--inactive']);
    }

    public function testRunActivationByToggleWhenNoMember()
    {
        $activeDto = new MemberDTO();
        $activeDto->active = false;

        $this->repo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('1'))
            ->willReturn($activeDto);

        $this->expectException(NoResultException::class);

        $this->repo->expects($this->once())
            ->method('activate')
            ->willThrowException(new NoResultException());

        $this->command->run(['--id=1']);
    }

    public function testRunActivateWhenNoMember()
    {
        $this->expectException(NothingToUpdateException::class);

        $this->repo->expects($this->once())
            ->method('activate')
            ->willThrowException(new NothingToUpdateException());

        $this->command->run(['--id=1', '--active']);
    }

    public function testRunDeactivateWhenNoMember()
    {
        $this->expectException(NothingToUpdateException::class);

        $this->repo->expects($this->once())
            ->method('deactivate')
            ->willThrowException(new NothingToUpdateException());

        $this->command->run(['--id=1', '--inactive']);
    }
}
