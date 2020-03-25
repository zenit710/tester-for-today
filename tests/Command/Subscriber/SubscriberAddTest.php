<?php

namespace Command\Subscriber;

use Acme\Command\Subscriber\SubscriberAdd;
use Acme\Command\MissingArgumentException;
use Acme\Entity\Subscriber\SubscriberDTO;
use Acme\Entity\Subscriber\SubscriberRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SubscriberAddTest extends TestCase
{
    /** @var SubscriberAdd */
    private $command;

    /** @var MockObject */
    private $repo;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->repo = $this->createMock(SubscriberRepository::class);
        $this->command = new SubscriberAdd($logger, $this->repo);
    }

    public function testRunWithoutRequiredParams()
    {
        $this->expectException(MissingArgumentException::class);
        $this->command->run([]);
    }

    public function testRunWithRequiredParams()
    {
        $subscriberDTO = new SubscriberDTO();
        $subscriberDTO->email = 'tester@test.pl';

        $this->repo->expects($this->once())
            ->method('add')
            ->with($this->equalTo($subscriberDTO));
        $this->command->run(['--email=tester@test.pl']);
    }
}
