<?php

namespace Acme\Command;

use Psr\Log\LoggerInterface;

/**
 * Class AbstractCommand
 * @package Acme\Command
 */
abstract class AbstractCommand
{
    const ARG_PATTERN = '/--\w+(=\w+)?/';
    const ARG_HELP = 'help';

    /** @var string */
    protected $commandName = '';

    /** @var array */
    protected $commandArgs = [];

    /** @var LoggerInterface */
    protected $logger;

    /**
     * AbstractCommand constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string[] $args
     * @return string
     * @throws \Exception
     */
    public abstract function run(array $args): string;

    /**
     * @return string
     */
    public abstract function help(): string;

    /**
     * @param string $name
     * @return bool
     */
    public function supports(string $name): bool
    {
        return $this->commandName === $name;
    }

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return $this->commandName;
    }

    /**
     * @param string[] $args
     */
    protected function mapArgs(array $args)
    {
        $commandArgs = [];

        foreach ($args as $arg) {
            if (preg_match(self::ARG_PATTERN, $arg)) {
                @list($key, $value) = explode('=', $arg);
                $key = substr($key, 2);

                $commandArgs[$key] = isset($value) ? $value : null;
            }
        }

        $this->commandArgs = $commandArgs;
    }

    /**
     * @param string[] $requiredArgs
     * @return bool
     */
    protected function validateArgs(array $requiredArgs)
    {
        foreach ($requiredArgs as $arg) {
            if (!array_key_exists($arg, $this->commandArgs)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function hasHelpArg(): bool
    {
        return $this->hasArg(self::ARG_HELP);
    }

    /**
     * @param string $name
     * @return bool
     */
    protected function hasArg(string $name): bool
    {
        return array_key_exists($name, $this->commandArgs);
    }

    /**
     * @param string $name
     * @return string|null
     */
    protected function getArg(string $name)
    {
        if ($this->hasArg($name)) {
            return $this->commandArgs[$name];
        }

        return null;
    }
}