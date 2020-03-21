<?php

namespace Acme\Migration\Migrations;

use Acme\Migration\Migration;

/**
 * Class Migration_20200320210200_create_schema
 * @package Acme\Migration
 */
class Migration_20200320210200_create_schema extends Migration
{
    /**
     * @inheritDoc
     */
    public function up(): string
    {
        return '
            CREATE TABLE IF NOT EXISTS member (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL UNIQUE,
                active INTEGER NOT NULL DEFAULT 1
            );

            CREATE TABLE IF NOT EXISTS subscriber (
                id INTEGER PRIMARY KEY,
                email TEXT NOT NULL UNIQUE,
                active INTEGER NOT NULL DEFAULT 1
            );

            CREATE TABLE IF NOT EXISTS tester (
                id INTEGER PRIMARY KEY,
                member_id INTEGER NOT NULL,
                date DATE NOT NULL,
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
        return '
            DROP TABLE IF EXISTS member;
            DROP TABLE IF EXISTS subscriber;
            DROP TABLE IF EXISTS tester;
        ';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Migration_20200320210200_create_schema';
    }
}