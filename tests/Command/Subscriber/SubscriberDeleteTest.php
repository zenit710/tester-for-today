<?php

namespace Command\Subscriber;

use Acme\Command\Subscriber\SubscriberDelete;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Subscriber\SubscriberRepository;
use Acme\Entity\NothingToDeleteException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SubscriberDeleteTest extends TestCase
{
    /** @var SubscriberDelete */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(SubscriberRepository::class);
        $this->command = new SubscriberDelete($logger, $this->repo);
    }

    public function testRunWithoutRequiredParams()
    {
        $this->expectException(MissingArgumentException::class);
        $this->command->run([]);
    }

    public function testRunWhenNoSubscriber()
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
