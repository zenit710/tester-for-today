<?php

namespace Command;

use Acme\Command\AbstractCommand;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AbstractCommandTest extends TestCase
{
    /** @var AbstractCommand */
    private $command;

    /** @var \ReflectionClass */
    private $refCommand;

    protected function setUp()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $this->command = new class($logger) extends AbstractCommand {
            protected $commandName = 'command:test';

            /**
             * @inheritDoc
             */
            public function run(array $args): string
            {
                return 'runned';
            }

            /**
             * @inheritDoc
             */
            public function help(): string
            {
                return 'help';
            }

        };
        $this->refCommand = new \ReflectionClass(AbstractCommand::class);
    }

    public function testSupports()
    {
        $this->assertTrue($this->command->supports('command:test'));
        $this->assertFalse($this->command->supports('command:bad'));
    }

    public function testGetCommandName()
    {
        $this->assertSame('command:test', $this->command->getCommandName());
    }

    public function testMapArgs()
    {
        $mapArgs = $this->refCommand->getMethod('mapArgs');
        $mapArgs->setAccessible(true);
        $mapArgs->invoke($this->command, ['--help', '--name=Janusz', '-e', 'options']);

        $commandArgsRef = $this->refCommand->getProperty('commandArgs');
        $commandArgsRef->setAccessible(true);
        $commandArgs = $commandArgsRef->getValue($this->command);

        $this->assertCount(2, $commandArgs);
        $this->assertArrayHasKey('help', $commandArgs);
        $this->assertArrayHasKey('name', $commandArgs);
        $this->assertSame('Janusz', $commandArgs['name']);
    }

    public function testValidateArgs()
    {
        $commandArgsRef = $this->refCommand->getProperty('commandArgs');
        $commandArgsRef->setAccessible(true);
        $commandArgsRef->setValue($this->command, ['name' => 'Janusz', 'help' => null]);

        $validateArgs = $this->refCommand->getMethod('validateArgs');
        $validateArgs->setAccessible(true);

        $this->assertTrue($validateArgs->invoke($this->command, ['name', 'help']));
        $this->assertFalse($validateArgs->invoke($this->command, ['id']));
    }

    public function testHasArg()
    {
        $commandArgsRef = $this->refCommand->getProperty('commandArgs');
        $commandArgsRef->setAccessible(true);
        $commandArgsRef->setValue($this->command, ['name' => 'Janusz', 'help' => null]);

        $hasArg = $this->refCommand->getMethod('hasArg');
        $hasArg->setAccessible(true);

        $this->assertTrue($hasArg->invoke($this->command, 'name'));
        $this->assertFalse($hasArg->invoke($this->command, 'id'));
    }

    public function testGetArg()
    {
        $commandArgsRef = $this->refCommand->getProperty('commandArgs');
        $commandArgsRef->setAccessible(true);
        $commandArgsRef->setValue($this->command, ['name' => 'Janusz', 'help' => null]);

        $getArg = $this->refCommand->getMethod('getArg');
        $getArg->setAccessible(true);

        $this->assertSame('Janusz', $getArg->invoke($this->command, 'name'));
        $this->assertSame(null, $getArg->invoke($this->command, 'id'));
    }

    public function testHasHelpArg()
    {
        $hasHelpArg = $this->refCommand->getMethod('hasHelpArg');
        $hasHelpArg->setAccessible(true);

        $this->assertFalse($hasHelpArg->invoke($this->command));

        $commandArgsRef = $this->refCommand->getProperty('commandArgs');
        $commandArgsRef->setAccessible(true);
        $commandArgsRef->setValue($this->command, ['name' => 'Janusz', 'help' => null]);

        $this->assertTrue($hasHelpArg->invoke($this->command));
    }
}
