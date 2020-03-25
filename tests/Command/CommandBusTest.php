<?php

namespace Command;

use Acme\Command\AbstractCommand;
use Acme\Command\CommandBus;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CommandBusTest extends TestCase
{
    /** @var CommandBus */
    private $bus;

    /** @var LoggerInterface */
    private $logger;

    protected function setUp()
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->bus = new CommandBus($this->logger);
    }

    public function testRegister()
    {
        $ref = new \ReflectionClass(CommandBus::class);
        $commandsProperty = $ref->getProperty('commands');
        $commandsProperty->setAccessible(true);

        $commands = $commandsProperty->getValue($this->bus);
        $this->assertCount(0, $commands); // no commands at the start

        $this->bus->register(new Command($this->logger));

        $commands = $commandsProperty->getValue($this->bus);
        $this->assertCount(1, $commands);; // should be one command after register

        $this->bus->register(new Command($this->logger));

        $commands = $commandsProperty->getValue($this->bus);
        $this->assertCount(1, $commands); // cannot register same command twice
    }

    public function testHandle()
    {
        $this->bus->register(new Command($this->logger));

        $this->assertSame('runned', $this->bus->handle('command:test', []));

        $runArgs = ['--a', '--b=c'];
        $this->assertSame(join(',', $runArgs), $this->bus->handle('command:test', $runArgs));
    }

    public function testAvailableCommands()
    {
        $this->bus->register(new Command($this->logger));

        $this->assertRegExp('/.+command:test.+/s', $this->bus->availableCommands());
    }
}

class Command extends AbstractCommand
{
    protected $commandName = 'command:test';

    /**
     * @inheritDoc
     */
    public function run(array $args): string
    {
        return count($args) ? join(',', $args) : 'runned';
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'help';
    }

}
