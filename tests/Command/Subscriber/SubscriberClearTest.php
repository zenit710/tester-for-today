<?php

namespace Command\Subscriber;

use Acme\Command\Subscriber\SubscriberClear;
use Acme\Entity\Subscriber\SubscriberRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SubscriberClearTest extends TestCase
{
    /** @var SubscriberClear */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(SubscriberRepository::class);
        $this->command = new SubscriberClear($logger, $this->repo);
    }

    public function testRun()
    {
        $this->repo->expects($this->once())
            ->method('clear');
        $this->command->run([]);
    }
}
