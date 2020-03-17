<?php

namespace Acme\Command;

use Psr\Log\LoggerInterface;

/**
 * Class CommandBus
 * @package Acme\Command
 */
class CommandBus
{
    /** @var AbstractCommand[] */
    private $commands = [];

    /** @var LoggerInterface */
    private $logger;

    /**
     * CommandBus constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param AbstractCommand $command
     */
    public function register(AbstractCommand $command)
    {
        foreach ($this->commands as $cmd) {
            if (get_class($cmd) === get_class($command)) {
                return;
            }
        }

        $this->commands[] = $command;
    }

    /**
     * @param string $commandName
     * @param string[] $args
     * @return string
     */
    public function handle(string $commandName, array $args)
    {
        foreach ($this->commands as $command) {
            if ($command->supports($commandName)) {
                try {
                    return $command->run($args);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage(), $e->getTrace());

                    return $e->getMessage() . PHP_EOL;
                }
            }
        }

        return $this->availableCommands();
    }

    /**
     * @return string
     */
    public function availableCommands(): string
    {
        $output = 'Usage: ' . PHP_EOL
            . 'php app.php command [option_1] [option_2]...' . PHP_EOL . PHP_EOL
            . 'Available commands: ' . PHP_EOL;

        foreach ($this->commands as $command) {
            $output .= "\t" . $command->getCommandName() . PHP_EOL;
        }

        $output .= PHP_EOL . 'Run desired command with --help to get more details' . PHP_EOL;

        return $output;
    }
}