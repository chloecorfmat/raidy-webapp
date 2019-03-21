<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190321145027 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        if (!$schema->getTable('competitor')->hasColumn('competitor1')) {
            $this->addSql('ALTER TABLE competitor CHANGE lastname competitor1 VARCHAR(255)');
        }

        if (!$schema->getTable('competitor')->hasColumn('competitor2')) {
            $this->addSql('ALTER TABLE competitor CHANGE firstname competitor2 VARCHAR(255)');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
