<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231214113447 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create table coupons';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE coupons (
                id SERIAL NOT NULL,
                code VARCHAR(255),
                type SMALLINT,
                value INTEGER,
                PRIMARY KEY(id)
            );
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE coupons;');
    }
}
