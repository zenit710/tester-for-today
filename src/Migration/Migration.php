<?php

namespace Acme\Migration;

use Acme\DbConnection;

/**
 * Class Migration
 * @package Acme\Migration
 */
abstract class Migration
{
    /** @var DbConnection */
    private $db;

    /**
     * Migration constructor.
     * @param DbConnection $db
     */
    public function __construct(DbConnection $db)
    {
        $this->db = $db;
    }

    /**
     * @return string
     */
    public abstract function up(): string;

    /**
     * @return string
     */
    public abstract function down(): string;

    /**
     * @return string
     */
    public abstract function getName(): string;

    /**
     * @return bool
     */
    private function isMigrated(): bool
    {
        $stmt = $this->db->getConnection()->prepare('
            SELECT COUNT(*) count FROM migration WHERE name = :name AND reverted = 0
        ');
        $stmt->bindValue(':name', $this->getName());

        $result = $stmt->execute();
        $count = $result->fetchArray()['count'];

        return $count > 0;
    }

    /**
     * @throws AlreadyMigratedException
     * @throws MigrationFailureException
     */
    public function apply()
    {
        if ($this->isMigrated()) {
            throw new AlreadyMigratedException($this->getName() . ' already migrated!');
        }

        try {
            $this->db->getConnection()->exec($this->up());
        } catch (\Exception $e) {
            throw new MigrationFailureException($this->getName() . ' migration failed!');
        }

        $stmt = $this->db->getConnection()->prepare('INSERT INTO migration (name) VALUES (:name)');
        $stmt->bindValue(':name', $this->getName());
        $stmt->execute();
    }

    /**
     * @throws MigrationFailureException
     * @throws NotMigratedException
     */
    public function revert()
    {
        if (!$this->isMigrated()) {
            throw new NotMigratedException($this->getName() . ' not migrated yet!');
        }

        $result = $this->db->getConnection()->exec($this->down());

        if (!$result) {
            throw new MigrationFailureException($this->getName() . ' revert failed!');
        }

        $stmt = $this->db->getConnection()->prepare('UPDATE migration SET reverted = 1 WHERE name = :name');
        $stmt->bindValue(':name', $this->getName());
        $stmt->execute();
    }
}