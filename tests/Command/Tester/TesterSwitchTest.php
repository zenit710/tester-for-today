<?php

namespace Command\Tester;

use Acme\Command\MissingArgumentException;
use Acme\Command\Tester\InactiveTesterException;
use Acme\Command\Tester\TesterSwitch;
use Acme\Entity\Absence\AbsenceDTO;
use Acme\Entity\Absence\AbsenceFilter;
use Acme\Entity\Absence\AbsenceRepository;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\Member\MemberRepository;
use Acme\Entity\NoResultException;
use Acme\Entity\Subscriber\SubscriberDTO;
use Acme\Entity\Subscriber\SubscriberFilter;
use Acme\Entity\Subscriber\SubscriberRepository;
use Acme\Entity\Tester\TesterDTO;
use Acme\Entity\Tester\TesterRepository;
use Acme\Service\Mail\MailService;
use PHPMailer\PHPMailer\PHPMailer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TesterSwitchTest extends TestCase
{
    /** @var TesterSwitch */
    private $command;

    /** @var MockObject */
    private $testerRepo;

    /** @var MockObject */
    private $memberRepo;

    /** @var MockObject */
    private $subRepo;

    /** @var MockObject */
    private $absenceRepo;

    /** @var MockObject */
    private $mailService;

    /** @var \ReflectionClass */
    private $refCommand;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->testerRepo= $this->createMock(TesterRepository::class);
        $this->memberRepo = $this->createMock(MemberRepository::class);
        $this->subRepo = $this->createMock(SubscriberRepository::class);
        $this->absenceRepo = $this->createMock(AbsenceRepository::class);
        $this->mailService = $this->createMock(MailService::class);
        $this->command = new TesterSwitch(
            $logger,
            $this->testerRepo,
            $this->memberRepo,
            $this->subRepo,
            $this->absenceRepo,
            $this->mailService
        );
        $this->refCommand = new \ReflectionClass(TesterSwitch::class);
    }

    public function testRunWithNoArgs()
    {
        $this->expectException(MissingArgumentException::class);

        $this->command->run([]);
    }

    public function testRunWithAutoArgWhenCannotChooseNextTester()
    {
        $this->expectException(NoResultException::class);

        $this->testerRepo->expects($this->once())
            ->method('getLastTester')
            ->willThrowException(new NoResultException());

        $this->memberRepo->expects($this->once())
            ->method('getNextActiveById')
            ->willThrowException(new NoResultException());

        $this->command->run(['--auto']);
    }

    public function testRunWithAutoArg()
    {
        $member = new MemberDTO();
        $member->id = 1;

        $tester = new TesterDTO();
        $tester->memberId = 1;

        $this->testerRepo->expects($this->once())
            ->method('getLastTester')
            ->willThrowException(new NoResultException());

        $this->memberRepo->expects($this->once())
            ->method('getNextActiveById')
            ->willReturn($member);

        $this->testerRepo->expects($this->once())
            ->method('add')
            ->with($this->equalTo($tester));

        $this->command->run(['--auto']);
    }

    public function testRunWithManualArgButWithoutIdArg()
    {
        $this->expectException(MissingArgumentException::class);

        $this->command->run(['--manual']);
    }

    public function testRunWithManualArgWhenMemberIdNotExist()
    {
        $this->expectException(NoResultException::class);

        $this->memberRepo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('99'))
            ->willThrowException(new NoResultException());

        $this->command->run(['--manual', '--id=99']);
    }

    public function testRunWithManualArgWhenMemberInactive()
    {
        $member = new MemberDTO();
        $member->active = false;

        $this->expectException(InactiveTesterException::class);

        $this->memberRepo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('99'))
            ->willReturn($member);

        $this->command->run(['--manual', '--id=99']);
    }

    public function testRunWithManualArgWhenMemberAbsent()
    {
        $member = new MemberDTO();
        $member->id = 99;
        $member->active = true;

        $this->expectException(InactiveTesterException::class);

        $this->absenceRepo->expects($this->once())
            ->method('getAll')
            ->willReturn([new AbsenceDTO()]);

        $this->memberRepo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('99'))
            ->willReturn($member);

        $this->command->run(['--manual', '--id=99']);
    }

    public function testRunWithManualArg()
    {
        $member = new MemberDTO();
        $member->id = 99;
        $member->active = true;

        $tester = new TesterDTO();
        $tester->memberId = 99;

        $this->absenceRepo->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $this->memberRepo->expects($this->once())
            ->method('getById')
            ->with($this->equalTo('99'))
            ->willReturn($member);

        $this->testerRepo->expects($this->once())
            ->method('add')
            ->with($this->equalTo($tester));

        $this->command->run(['--manual', '--id=99']);
    }

    public function testGetLastTesterIdWhenNoCurrentTester()
    {
        $this->testerRepo->expects($this->once())
            ->method('getLastTester')
            ->willThrowException(new NoResultException());

        $methodRef = $this->refCommand->getMethod('getLastTesterId');
        $methodRef->setAccessible(true);

        $this->assertSame(0, $methodRef->invoke($this->command));
    }

    public function testGetLastTesterId()
    {
        $member = new MemberDTO();
        $member->id = 99;

        $this->testerRepo->expects($this->once())
            ->method('getLastTester')
            ->willReturn($member);

        $methodRef = $this->refCommand->getMethod('getLastTesterId');
        $methodRef->setAccessible(true);

        $this->assertSame(99, $methodRef->invoke($this->command));
    }

    public function testIsMemberAbsentWhenNoAbsent()
    {
        $this->absenceRepo->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $methodRef = $this->refCommand->getMethod('isMemberAbsent');
        $methodRef->setAccessible(true);

        $this->assertFalse($methodRef->invoke($this->command, 1));
    }

    public function testIsMemberAbsent()
    {
        $filter = new AbsenceFilter();
        $filter->setMemberId(1);
        $filter->setStartsTo(date('Y-m-d'));
        $filter->setEndsFrom(date('Y-m-d'));

        $this->absenceRepo->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo($filter))
            ->willReturn([new AbsenceDTO()]);

        $methodRef = $this->refCommand->getMethod('isMemberAbsent');
        $methodRef->setAccessible(true);

        $this->assertTrue($methodRef->invoke($this->command, 1));
    }

    public function testNotifySubscribersWhenHasSubscribers()
    {
        $tester = new MemberDTO();
        $tester->name = 'Janusz';

        $filter = new SubscriberFilter();
        $filter->setActive(true);

        $sub = new SubscriberDTO();
        $sub->email = 'test@test.pl';

        $mailMock = $this->createMock(PHPMailer::class);
        $mailMock->expects($this->once())
            ->method('addBcc');
        $mailMock->expects($this->once())
            ->method('send');

        $this->subRepo->expects($this->once())
            ->method('getAll')
            ->with($this->equalTo($filter))
            ->willReturn([$sub]);

        $this->mailService->expects($this->once())
            ->method('create')
            ->willReturn($mailMock);

        $methodRef = $this->refCommand->getMethod('notifySubscribers');
        $methodRef->setAccessible(true);

        $methodRef->invoke($this->command, $tester);
    }

    public function testNotifySubscribersWhenNoSubscribers()
    {
        $tester = new MemberDTO();
        $tester->name = 'Janusz';

        $this->subRepo->expects($this->once())
            ->method('getAll')
            ->willReturn([]);

        $methodRef = $this->refCommand->getMethod('notifySubscribers');
        $methodRef->setAccessible(true);

        $methodRef->invoke($this->command, $tester);
    }
}
