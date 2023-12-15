<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231214113913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create table taxes';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE taxes (
                id SERIAL NOT NULL,
                country VARCHAR(255),
                percent INTEGER,
                tax_number VARCHAR(255),
                PRIMARY KEY(id)
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE taxes;');
    }
}
