<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251007194724 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds a set null on delete for dpo and data responsible foreign keys on client entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C74404552130F757');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT FK_C7440455BB6058D7');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C74404552130F757 FOREIGN KEY (dpo_id) REFERENCES person (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455BB6058D7 FOREIGN KEY (data_responsible_id) REFERENCES person (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE client DROP CONSTRAINT fk_c7440455bb6058d7');
        $this->addSql('ALTER TABLE client DROP CONSTRAINT fk_c74404552130f757');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT fk_c7440455bb6058d7 FOREIGN KEY (data_responsible_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT fk_c74404552130f757 FOREIGN KEY (dpo_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
