<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251014205347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracker_consent DROP CONSTRAINT fk_344d75fc41079d63');
        $this->addSql('ALTER TABLE tracker_consent ADD CONSTRAINT FK_344D75FC41079D63 FOREIGN KEY (consent_id) REFERENCES consent (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tracker_consent DROP CONSTRAINT FK_344D75FC41079D63');
        $this->addSql('ALTER TABLE tracker_consent ADD CONSTRAINT fk_344d75fc41079d63 FOREIGN KEY (consent_id) REFERENCES consent (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
