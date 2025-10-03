<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251003161138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initialize database schema';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE admin_user (id UUID NOT NULL, agency_id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, google_id VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_AD8A54A9E7927C74 ON admin_user (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AD8A54A9CDEADB2A ON admin_user (agency_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN admin_user.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN admin_user.agency_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN admin_user.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN admin_user.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE agency (id UUID NOT NULL, name VARCHAR(255) NOT NULL, host VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN agency.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN agency.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN agency.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE client (id UUID NOT NULL, agency_id UUID NOT NULL, data_responsible_id UUID DEFAULT NULL, dpo_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7440455CDEADB2A ON client (agency_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C7440455BB6058D7 ON client (data_responsible_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C74404552130F757 ON client (dpo_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN client.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN client.agency_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN client.data_responsible_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN client.dpo_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN client.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN client.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE consent (id UUID NOT NULL, website_id UUID NOT NULL, user_id VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_63120810A76ED395 ON consent (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6312081018F45C82 ON consent (website_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6312081018F45C82A76ED395 ON consent (website_id, user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6312081018F45C828B8E8428 ON consent (website_id, created_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN consent.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN consent.website_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN consent.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN consent.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE gdprtreatment (id UUID NOT NULL, client_id UUID NOT NULL, name VARCHAR(255) NOT NULL, ref VARCHAR(255) NOT NULL, processing_purpose TEXT NOT NULL, processing_sub_purpose1 TEXT DEFAULT NULL, processing_sub_purpose2 TEXT DEFAULT NULL, processing_sub_purpose3 TEXT DEFAULT NULL, processing_sub_purpose4 TEXT DEFAULT NULL, processing_sub_purpose5 TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2536E23A19EB6921 ON gdprtreatment (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN gdprtreatment.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN gdprtreatment.client_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN gdprtreatment.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN gdprtreatment.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE person (id UUID NOT NULL, client_id UUID NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, post_code VARCHAR(7) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_34DCD17619EB6921 ON person (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN person.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN person.client_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN person.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN person.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE personal_data_category (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_category.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_category.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_category.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE personal_data_treatment_category (id UUID NOT NULL, category_id UUID NOT NULL, treatment_id UUID NOT NULL, description TEXT NOT NULL, duration TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30CAF3C912469DE2 ON personal_data_treatment_category (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30CAF3C9471C0366 ON personal_data_treatment_category (treatment_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_treatment_category.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_treatment_category.category_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_treatment_category.treatment_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_treatment_category.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN personal_data_treatment_category.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (id INT NOT NULL, user_id UUID NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.user_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.requested_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.expires_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sensitive_data_category (id UUID NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_category.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_category.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_category.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sensitive_data_treatment_category (id UUID NOT NULL, category_id UUID NOT NULL, treatment_id UUID NOT NULL, description TEXT NOT NULL, duration TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7EC6612A12469DE2 ON sensitive_data_treatment_category (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7EC6612A471C0366 ON sensitive_data_treatment_category (treatment_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_treatment_category.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_treatment_category.category_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_treatment_category.treatment_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_treatment_category.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN sensitive_data_treatment_category.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE server (id UUID NOT NULL, agency_id UUID NOT NULL, name VARCHAR(255) NOT NULL, quantity_of_co2eq_per_year INT NOT NULL, number_of_unmanaged_sites INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_5A6DD5F6CDEADB2A ON server (agency_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN server.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN server.agency_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN server.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN server.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tracker (id UUID NOT NULL, website_id UUID NOT NULL, type VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, tracker_id VARCHAR(255) NOT NULL, custom_code TEXT DEFAULT NULL, custom_url VARCHAR(1024) DEFAULT NULL, use_default_snippet BOOLEAN DEFAULT true NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_AC632AAF18F45C82 ON tracker (website_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker.website_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tracker_consent (id UUID NOT NULL, consent_id UUID NOT NULL, tracker_id UUID NOT NULL, accepted BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_344D75FC41079D63 ON tracker_consent (consent_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_344D75FCFB5230B ON tracker_consent (tracker_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker_consent.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker_consent.consent_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker_consent.tracker_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker_consent.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN tracker_consent.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE treatment_concerned_person_category (id UUID NOT NULL, treatment_id UUID DEFAULT NULL, person_category VARCHAR(255) NOT NULL, details VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BAA46895471C0366 ON treatment_concerned_person_category (treatment_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_concerned_person_category.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_concerned_person_category.treatment_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_concerned_person_category.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_concerned_person_category.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE treatment_out_of_eutransfer (id UUID NOT NULL, treatment_id UUID DEFAULT NULL, recipient VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, warranty_type VARCHAR(255) NOT NULL, documentation_link VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1EB2F4B6471C0366 ON treatment_out_of_eutransfer (treatment_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_out_of_eutransfer.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_out_of_eutransfer.treatment_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_out_of_eutransfer.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_out_of_eutransfer.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE treatment_recipient_type (id UUID NOT NULL, treatment_id UUID DEFAULT NULL, recipient_type VARCHAR(255) NOT NULL, details VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E0111A97471C0366 ON treatment_recipient_type (treatment_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_recipient_type.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_recipient_type.treatment_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_recipient_type.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_recipient_type.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE treatment_security_measure (id UUID NOT NULL, treatment_id UUID DEFAULT NULL, security_measure VARCHAR(255) NOT NULL, details VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C3208733471C0366 ON treatment_security_measure (treatment_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_security_measure.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_security_measure.treatment_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_security_measure.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN treatment_security_measure.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE website (id UUID NOT NULL, client_id UUID NOT NULL, server_id UUID DEFAULT NULL, respect_do_not_track BOOLEAN DEFAULT true NOT NULL, show_open_button BOOLEAN DEFAULT true NOT NULL, dialog_title VARCHAR(255) DEFAULT NULL, dialog_text TEXT DEFAULT NULL, custom_css TEXT DEFAULT NULL, add_access_log_to_gdpr BOOLEAN DEFAULT false NOT NULL, add_tracker_to_gdpr BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_476F5DE719EB6921 ON website (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_476F5DE71844E6B7 ON website (server_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website.client_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website.server_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE website_domain (id UUID NOT NULL, website_id UUID NOT NULL, domain VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_352CFA2018F45C82 ON website_domain (website_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_352CFA20A7A91E0B ON website_domain (domain)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website_domain.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website_domain.website_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website_domain.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website_domain.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE website_hit (id UUID NOT NULL, website_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ip_address VARCHAR(255) NOT NULL, referer VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_32A8588218F45C82 ON website_hit (website_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_32A8588218F45C828B8E8428 ON website_hit (website_id, created_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website_hit.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website_hit.website_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN website_hit.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE admin_user ADD CONSTRAINT FK_AD8A54A9CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C7440455CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C7440455BB6058D7 FOREIGN KEY (data_responsible_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C74404552130F757 FOREIGN KEY (dpo_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consent ADD CONSTRAINT FK_6312081018F45C82 FOREIGN KEY (website_id) REFERENCES website (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gdprtreatment ADD CONSTRAINT FK_2536E23A19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person ADD CONSTRAINT FK_34DCD17619EB6921 FOREIGN KEY (client_id) REFERENCES client (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE personal_data_treatment_category ADD CONSTRAINT FK_30CAF3C912469DE2 FOREIGN KEY (category_id) REFERENCES personal_data_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE personal_data_treatment_category ADD CONSTRAINT FK_30CAF3C9471C0366 FOREIGN KEY (treatment_id) REFERENCES gdprtreatment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES admin_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sensitive_data_treatment_category ADD CONSTRAINT FK_7EC6612A12469DE2 FOREIGN KEY (category_id) REFERENCES sensitive_data_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sensitive_data_treatment_category ADD CONSTRAINT FK_7EC6612A471C0366 FOREIGN KEY (treatment_id) REFERENCES gdprtreatment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE server ADD CONSTRAINT FK_5A6DD5F6CDEADB2A FOREIGN KEY (agency_id) REFERENCES agency (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tracker ADD CONSTRAINT FK_AC632AAF18F45C82 FOREIGN KEY (website_id) REFERENCES website (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tracker_consent ADD CONSTRAINT FK_344D75FC41079D63 FOREIGN KEY (consent_id) REFERENCES consent (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tracker_consent ADD CONSTRAINT FK_344D75FCFB5230B FOREIGN KEY (tracker_id) REFERENCES tracker (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_concerned_person_category ADD CONSTRAINT FK_BAA46895471C0366 FOREIGN KEY (treatment_id) REFERENCES gdprtreatment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_out_of_eutransfer ADD CONSTRAINT FK_1EB2F4B6471C0366 FOREIGN KEY (treatment_id) REFERENCES gdprtreatment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_recipient_type ADD CONSTRAINT FK_E0111A97471C0366 FOREIGN KEY (treatment_id) REFERENCES gdprtreatment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_security_measure ADD CONSTRAINT FK_C3208733471C0366 FOREIGN KEY (treatment_id) REFERENCES gdprtreatment (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website ADD CONSTRAINT FK_476F5DE719EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website ADD CONSTRAINT FK_476F5DE71844E6B7 FOREIGN KEY (server_id) REFERENCES server (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website_domain ADD CONSTRAINT FK_352CFA2018F45C82 FOREIGN KEY (website_id) REFERENCES website (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website_hit ADD CONSTRAINT FK_32A8588218F45C82 FOREIGN KEY (website_id) REFERENCES website (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE reset_password_request_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE admin_user DROP CONSTRAINT FK_AD8A54A9CDEADB2A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP CONSTRAINT FK_C7440455CDEADB2A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP CONSTRAINT FK_C7440455BB6058D7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP CONSTRAINT FK_C74404552130F757
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE consent DROP CONSTRAINT FK_6312081018F45C82
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gdprtreatment DROP CONSTRAINT FK_2536E23A19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE person DROP CONSTRAINT FK_34DCD17619EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE personal_data_treatment_category DROP CONSTRAINT FK_30CAF3C912469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE personal_data_treatment_category DROP CONSTRAINT FK_30CAF3C9471C0366
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sensitive_data_treatment_category DROP CONSTRAINT FK_7EC6612A12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sensitive_data_treatment_category DROP CONSTRAINT FK_7EC6612A471C0366
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE server DROP CONSTRAINT FK_5A6DD5F6CDEADB2A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tracker DROP CONSTRAINT FK_AC632AAF18F45C82
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tracker_consent DROP CONSTRAINT FK_344D75FC41079D63
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tracker_consent DROP CONSTRAINT FK_344D75FCFB5230B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_concerned_person_category DROP CONSTRAINT FK_BAA46895471C0366
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_out_of_eutransfer DROP CONSTRAINT FK_1EB2F4B6471C0366
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_recipient_type DROP CONSTRAINT FK_E0111A97471C0366
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE treatment_security_measure DROP CONSTRAINT FK_C3208733471C0366
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website DROP CONSTRAINT FK_476F5DE719EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website DROP CONSTRAINT FK_476F5DE71844E6B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website_domain DROP CONSTRAINT FK_352CFA2018F45C82
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE website_hit DROP CONSTRAINT FK_32A8588218F45C82
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE admin_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE agency
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE consent
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE gdprtreatment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE person
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE personal_data_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE personal_data_treatment_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sensitive_data_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sensitive_data_treatment_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE server
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tracker
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tracker_consent
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE treatment_concerned_person_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE treatment_out_of_eutransfer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE treatment_recipient_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE treatment_security_measure
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE website
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE website_domain
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE website_hit
        SQL);
    }
}
