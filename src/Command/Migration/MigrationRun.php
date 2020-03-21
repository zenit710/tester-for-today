<?php

namespace Acme\Command\Migration;

use Acme\ClassDiscover;
use Acme\Command\AbstractCommand;
use Acme\Command\Migration\Helper\MigrationState;
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
    const BASE_MIGRATION_CLASS = 'Acme\\Migration\\Migration';
    const MIGRATION_CLASS_PREFIX =  '\\Acme\\Migration\\Migrations\\';
    const MIGRATION_FILE_PATTERN = ROOTPATH . '/src/Migration/Migrations/*.php';
    const ARG_REVERT = 'revert';
    const ARG_NAME = 'name';

    /** @var string */
    protected $commandName = 'migration:run';

    /** @var DbConnection */
    private $db;

    /** @var ClassDiscover */
    private $classDiscover;

    /** @var bool */
    private $isRevert = false;

    /**
     * MigrationRun constructor.
     * @param LoggerInterface $logger
     * @param DbConnection $db
     * @param ClassDiscover $classDiscover
     */
    public function __construct(LoggerInterface $logger, DbConnection $db, ClassDiscover $classDiscover)
    {
        parent::__construct($logger);
        $this->db = $db;
        $this->classDiscover = $classDiscover;

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

        $this->isRevert = $this->hasArg(self::ARG_REVERT);

        if ($this->hasArg(self::ARG_NAME)) {
            $migrationState = $this->handleSingleMigration($this->getArg(self::ARG_NAME));
        } else {
            $migrationClassNames = $this->classDiscover->getAllByPattern(self::MIGRATION_FILE_PATTERN);
            $migrations = $this->instantiateMigrationsArray($migrationClassNames);
            $migrationsCount = count($migrations);
            $this->logger->info('Checking ' . $migrationsCount . ' migrations.');

            $migrationState = $this->handleMigrations($migrations);
        }

        return $migrationState;
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
     * @return MigrationState
     */
    private function handleSingleMigration(string $name): MigrationState
    {
        $migration = $this->instantiateMigrationByClassName($name);

        if (is_null($migration)) {
            return new MigrationState();
        }

        return $this->handleMigrations([$migration]);
    }

    /**
     * @param string $className
     * @return Migration|null
     */
    private function instantiateMigrationByClassName(string $className)
    {
        $class = self::MIGRATION_CLASS_PREFIX . $className;

        if (!$this->canHandleMigration($class)) {
            return null;
        }

        return new $class($this->db);
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
     * @param string[] $migrationNames
     * @return Migration[]
     */
    private function instantiateMigrationsArray(array $migrationNames): array
    {
        $migrations = [];

        foreach ($migrationNames as $name) {
            $migration = $this->instantiateMigrationByClassName($name);

            if (!is_null($migration)) {
                $migrations[] = $migration;
            }
        }

        return $migrations;
    }

    /**
     * @param Migration[] $migrations
     * @return MigrationState
     */
    private function handleMigrations(array $migrations): MigrationState
    {
        $state = new MigrationState();

        foreach ($migrations as $migration) {
            try {
                if ($this->isRevert) {
                    $migration->revert();
                } else {
                    $migration->apply();
                }

                $state->addSuccessful($migration);
            } catch (NotMigratedException $e) {
            } catch (AlreadyMigratedException $e) {
            } catch (MigrationFailureException $e) {
                $this->logger->error($e->getMessage(), $e->getTrace());
                $state->addFailed($migration);
            }
        }

        return $state;
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