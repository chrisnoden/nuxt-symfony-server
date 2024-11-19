<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240820151414 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'core tables - user, client, reset, etc';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE clients_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_reset_password_requests_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('CREATE TABLE clients (id INT NOT NULL, company_name VARCHAR(100) NOT NULL, enabled BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE TABLE user_confirm_email_requests (id UUID NOT NULL, user_id UUID NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6F6FEE27E7927C74 ON user_confirm_email_requests (email)');
        $this->addSql('CREATE INDEX IDX_6F6FEE27A76ED395 ON user_confirm_email_requests (user_id)');
        $this->addSql('COMMENT ON COLUMN user_confirm_email_requests.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_confirm_email_requests.user_id IS \'(DC2Type:uuid)\'');

        $this->addSql('CREATE TABLE user_reset_password_requests (id INT NOT NULL, user_id UUID NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_AFB5C210A76ED395 ON user_reset_password_requests (user_id)');
        $this->addSql('COMMENT ON COLUMN user_reset_password_requests.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_reset_password_requests.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_reset_password_requests.expires_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql('CREATE TABLE users (id UUID NOT NULL, client_id INT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, roles JSONB NOT NULL, password VARCHAR(255) DEFAULT NULL, enabled BOOLEAN NOT NULL, two_factor_status VARCHAR(20) NOT NULL, google_authenticator_secret VARCHAR(255) DEFAULT NULL, auth_code VARCHAR(12) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE INDEX IDX_1483A5E919EB6921 ON users (client_id)');
        $this->addSql('COMMENT ON COLUMN users.id IS \'(DC2Type:uuid)\'');

        $this->addSql('ALTER TABLE user_confirm_email_requests ADD CONSTRAINT FK_6F6FEE27A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_reset_password_requests ADD CONSTRAINT FK_AFB5C210A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E919EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE clients_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_reset_password_requests_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_confirm_email_requests DROP CONSTRAINT FK_6F6FEE27A76ED395');
        $this->addSql('ALTER TABLE user_reset_password_requests DROP CONSTRAINT FK_AFB5C210A76ED395');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E919EB6921');
        $this->addSql('DROP TABLE clients');
        $this->addSql('DROP TABLE user_confirm_email_requests');
        $this->addSql('DROP TABLE user_reset_password_requests');
        $this->addSql('DROP TABLE users');
    }
}
