<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251014201829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent DROP CONSTRAINT fk_6312081018f45c82');
        $this->addSql('ALTER TABLE consent ADD CONSTRAINT FK_6312081018F45C82 FOREIGN KEY (website_id) REFERENCES website (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent DROP CONSTRAINT FK_6312081018F45C82');
        $this->addSql('ALTER TABLE consent ADD CONSTRAINT fk_6312081018f45c82 FOREIGN KEY (website_id) REFERENCES website (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
