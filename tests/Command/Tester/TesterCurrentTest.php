<?php

namespace Command\Tester;

use Acme\Command\Tester\TesterCurrent;
use Acme\Entity\Member\MemberDTO;
use Acme\Entity\NoResultException;
use Acme\Entity\Tester\TesterRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TesterCurrentTest extends TestCase
{
    /** @var TesterCurrent */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(TesterRepository::class);
        $this->command = new TesterCurrent($logger, $this->repo);
    }

    public function testRun()
    {
        $tester = new MemberDTO();
        $tester->id = 1;
        $tester->name = 'Janusz';

        $this->repo->expects($this->once())
            ->method('getLastTester')
            ->willReturn($tester);

        $this->assertSame('Current tester is: Janusz (id: 1)' . PHP_EOL, $this->command->run([]));
    }

    public function testRunWhenNoTester()
    {
        $this->repo->expects($this->once())
            ->method('getLastTester')
            ->willThrowException(new NoResultException());

        $this->assertSame(TesterCurrent::NO_TESTER_MESSAGE, $this->command->run([]));
    }
}
