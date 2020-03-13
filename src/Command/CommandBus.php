<?php

namespace Acme\Command;

/**
 * Class CommandBus
 * @package Acme\Command
 */
class CommandBus
{
    /** @var AbstractCommand[] */
    private $commands = [];

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
                    return $e->getMessage();
                }
            }
        }

        return "There is no such command!";
    }
}