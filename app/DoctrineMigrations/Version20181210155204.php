<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181210155204 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf(
            $schema->getTable('raid')->hasPrimaryKey('uniqid'),
            'Skipping because `uniqid` primary key exists.'
        );

        $this->addSql('ALTER TABLE raid CHANGE uniqid uniqid VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX raid_uniqid ON raid (uniqid)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
