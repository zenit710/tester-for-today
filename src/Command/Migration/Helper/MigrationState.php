<?php

namespace Acme\Command\Migration\Helper;

use Acme\Migration\Migration;

/**
 * Class MigrationState
 * @package Acme\Command\Migration\Helper
 */
class MigrationState
{
    /** @var Migration[] */
    private $successful = [];

    /** @var Migration[] */
    private $failed = [];

    /**
     * @param Migration $migration
     */
    public function addSuccessful(Migration $migration)
    {
        $this->successful[] = $migration;
    }

    /**
     * @param Migration $migration
     */
    public function addFailed(Migration $migration)
    {
        $this->failed[] = $migration;
    }

    /**
     * @return Migration[]
     */
    public function getSuccessful(): array
    {
        return $this->successful;
    }

    /**
     * @return Migration[]
     */
    public function getFailed(): array
    {
        return $this->failed;
    }

    /**
     * @return bool
     */
    public function hasSuccessful(): bool
    {
        return !empty($this->successful);
    }

    /**
     * @return bool
     */
    public function hasFailed(): bool
    {
        return !empty($this->failed);
    }

    public function __toString()
    {
        $output = '';

        if ($this->hasSuccessful()) {
            $output .= 'Successful migrations: ' . PHP_EOL;

            foreach ($this->successful as $migration) {
                $output .= "\t" . $migration->getName() . PHP_EOL;
            }
        }

        if ($this->hasFailed()) {
            $output .= 'Failed migrations: ' . PHP_EOL;

            foreach ($this->failed as $migration) {
                $output .= "\t" . $migration->getName() . PHP_EOL;
            }
        }

        if ($output == '') {
            $output = 'No migrations made' . PHP_EOL;
        }

        return $output;
    }
}