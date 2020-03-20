<?php

namespace Acme\Command\Migration;

use Acme\Command\AbstractCommand;
use Acme\DbConnection;
use Acme\Migration\AlreadyMigratedException;
use Acme\Migration\Migration;
use Acme\Migration\MigrationFailureException;
use Acme\Migration\NotMigratedException;
use Psr\Log\LoggerInterface;

/**
 * Class MigrationRun
 * @package Acme\Command\Migration
 */
class MigrationRun extends AbstractCommand
{
    const SUCCESS_MESSAGE = 'Migration handled!' . PHP_EOL;
    const MIGRATION_CLASS_PREFIX =  '\\Acme\\Migration\\Migrations\\';
    const MIGRATION_FILE_PATTERN = ROOTPATH . '/src/Migration/Migrations/*.php';
    const OPERATION_REVERT = 'revert';
    const OPERATION_APPLY = 'apply';
    const ARG_REVERT = 'revert';
    const ARG_NAME = 'name';

    /** @var string */
    protected $commandName = 'migration:run';

    /** @var DbConnection */
    private $db;

    /**
     * MigrationRun constructor.
     * @param LoggerInterface $logger
     * @param DbConnection $db
     */
    public function __construct(LoggerInterface $logger, DbConnection $db)
    {
        parent::__construct($logger);
        $this->db = $db;

        $this->createMigrationsTableSchema();
    }

    /**
     * @inheritDoc
     */
    public function run(array $args): string
    {
        $this->mapArgs($args);

        if ($this->hasHelpArg()) {
            return $this->help();
        }

        $operation = $this->hasArg(self::ARG_REVERT) ? 'revert' : 'apply';

        if ($this->hasArg(self::ARG_NAME)) {
            $this->handleMigration($this->getArg(self::ARG_NAME), $operation);
        } else {
            $migrations = $this->getAllMigrations();
            $migrationsCount = count($migrations);
            $results = [];

            foreach ($migrations as $migration) {
                try {
                    $results[] = $this->handleMigration($migration, $operation);
                } catch (NotMigratedException $e) { // if not migrated yet it's ok
                    $results[] = false;
                } catch (AlreadyMigratedException $e) { // if already migrated it's ok
                    $results[] = false;
                }
            }

            // revert all migrations if sth went wrong
            if (self::OPERATION_APPLY == $operation && in_array(false, $results)) {
                $this->logger->error('Migration failed. Reverting successful migrations.');

                for ($i = 0; $i < $migrationsCount; $i++) {
                    if ($results[$i]) {
                        $this->handleMigration($migrations[$i], self::OPERATION_REVERT);
                    }
                }
            }
        }

        return self::SUCCESS_MESSAGE;
    }

    private function createMigrationsTableSchema()
    {
        $this->db->getConnection()->exec('
            CREATE TABLE IF NOT EXISTS migration (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                reverted INTEGER DEFAULT 0 
            );
        ');
    }

    /**
     * @param string $name
     * @param string $operation
     * @return bool
     * @throws AlreadyMigratedException
     * @throws NotMigratedException
     */
    private function handleMigration(string $name, string $operation): bool
    {
        $class = self::MIGRATION_CLASS_PREFIX . $name;

        if ($this->canHandleMigration($class)) {
            /** @var Migration $migration */
            $migration = new $class($this->db);

            if (self::OPERATION_REVERT == $operation) {
                try {
                    $migration->revert();

                    return true;
                } catch (MigrationFailureException $e) { // report if revert failed
                    $this->logger->error($e->getMessage(), $e->getTrace());

                    return false;
                }
            } else {
                try {
                    $migration->apply();

                    return true;
                } catch (MigrationFailureException $e) { // report if migration failed
                    $this->logger->error($e->getMessage(), $e->getTrace());

                    return false;
                }
            }
        }

        return false;
    }

    /**
     * @param string $class
     * @return bool
     */
    private function canHandleMigration(string $class): bool
    {
        return class_exists($class) && in_array('Acme\\Migration\\Migration', class_parents($class));
    }

    /**
     * @return string[]
     */
    private function getAllMigrations(): array
    {
        $migrations = [];

        foreach (glob(self::MIGRATION_FILE_PATTERN) as $file) {
            $migrations[] = basename($file, '.php');
        }

        return $migrations;
    }

    /**
     * @inheritDoc
     */
    public function help(): string
    {
        return 'Run database migration' . PHP_EOL
            . PHP_EOL
            . 'Usage:' . PHP_EOL
            . $this->commandName . ' options' . PHP_EOL
            . "\t options: " . PHP_EOL
            . "\t --name=name - migration name" . PHP_EOL
            . "\t --revert - revert migration" . PHP_EOL
            . "\t --help - get help" . PHP_EOL;
    }
}