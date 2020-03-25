<?php

namespace Command\Subscriber;

use Acme\Command\Subscriber\SubscriberList;
use Acme\Entity\Subscriber\SubscriberFilter;
use Acme\Entity\Subscriber\SubscriberRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SubscriberListTest extends TestCase
{
    /** @var SubscriberList */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(SubscriberRepository::class);
        $this->command = new SubscriberList($logger, $this->repo);
    }

    public function testRun()
    {
        $filter = new SubscriberFilter();
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

        $this->assertSame(SubscriberList::NO_SUBSCRIBERS_MESSAGE, $this->command->run([]));
    }
}
