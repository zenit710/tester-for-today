<?php

namespace Acme\Migration\Migrations;

use Acme\Migration\Migration;

/**
 * Class Migration_20200320221100_add_absence_table
 * @package Acme\Migration\Migrations
 */
class Migration_20200320221100_add_absence_table extends Migration
{
    /**
     * @inheritDoc
     */
    public function up(): string
    {
        return '
            CREATE TABLE IF NOT EXISTS absence (
                id INTEGER PRIMARY KEY,
                member_id INTEGER NOT NULL,
                date_from DATE NOT NULL,
                date_to DATE NOT NULL,
                canceled INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY (member_id)
                    REFERENCES member (id)
            );
        ';
    }

    /**
     * @inheritDoc
     */
    public function down(): string
    {
        return 'DROP TABLE IF EXISTS absence';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Migration_20200320221100_add_absence_table';
    }

}