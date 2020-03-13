<?php

namespace Acme\Command;

/**
 * Class AbstractCommand
 * @package Acme\Command
 */
abstract class AbstractCommand
{
    const ARG_PATTERN = '/--\w+(=\w+)?/';
    const ARG_HELP = 'help';

    protected $commandArgs = [];

    /**
     * @param string $name
     * @return bool
     */
    public abstract function supports(string $name): bool;

    /**
     * @param string[] $args
     * @return string
     * @throws MissingArgumentException
     */
    public abstract function run(array $args): string;

    /**
     * @return string
     */
    public abstract function help(): string;

    /**
     * @param string[] $args
     */
    protected function mapArgs(array $args)
    {
        $commandArgs = [];

        foreach ($args as $arg) {
            if (preg_match(self::ARG_PATTERN, $arg)) {
                list($key, $value) = explode('=', $arg);
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
        return in_array(self::ARG_HELP, $this->commandArgs);
    }
}