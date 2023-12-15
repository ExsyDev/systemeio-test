<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231214104901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create table products';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE products (
                id SERIAL NOT NULL,
                name VARCHAR(255),
                price_euro INTEGER,
                PRIMARY KEY(id)
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE products;');
    }
}
