<?php

namespace Command\Tester;

use Acme\Command\Tester\TesterClear;
use Acme\Entity\Tester\TesterRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TesterClearTest extends TestCase
{
    /** @var TesterClear */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(TesterRepository::class);
        $this->command = new TesterClear($logger, $this->repo);
    }

    public function testRun()
    {
        $this->repo->expects($this->once())
            ->method('clear');
        $this->command->run([]);
    }
}
