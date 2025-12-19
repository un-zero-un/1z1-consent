<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251219161632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds auto_open field to website entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website ADD auto_open BOOLEAN NOT NULL DEFAULT TRUE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE website DROP auto_open');
    }
}
