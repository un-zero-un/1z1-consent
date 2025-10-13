<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251013170312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds a GPC enabled column to the consent table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent ADD gpc_enabled BOOLEAN DEFAULT FALSE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent DROP gpc_enabled');
    }
}
