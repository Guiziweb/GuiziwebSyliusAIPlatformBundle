<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251019063404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->skipIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform &&
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform,
            'Migration can only be executed safely on \'mysql\' or \'postgresql\'.'
        );

        // Platform configuration table
        $this->addSql('CREATE TABLE guiziweb_ai_platform_configuration (id INT NOT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, provider VARCHAR(50) NOT NULL, apiKey VARCHAR(255) DEFAULT NULL, settings JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_380D258677153098 ON guiziweb_ai_platform_configuration (code)');

        // Agent configuration table
        $this->addSql('CREATE TABLE guiziweb_ai_agent_configuration (id INT NOT NULL, platform_configuration_id INT NOT NULL, channel_id INT NOT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, model VARCHAR(100) DEFAULT NULL, systemPrompt TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D5F8E5FF77153098 ON guiziweb_ai_agent_configuration (code)');
        $this->addSql('CREATE INDEX IDX_D5F8E5FF3F4929AD ON guiziweb_ai_agent_configuration (platform_configuration_id)');
        $this->addSql('CREATE INDEX IDX_D5F8E5FF72F5A1AA ON guiziweb_ai_agent_configuration (channel_id)');

        // Agent tool table
        $this->addSql('CREATE TABLE guiziweb_ai_agent_tool (id INT NOT NULL, agent_configuration_id INT NOT NULL, toolName VARCHAR(255) NOT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98474D06DD7CCC62 ON guiziweb_ai_agent_tool (agent_configuration_id)');

        // Add sequences for PostgreSQL
        if ($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform) {
            $this->addSql('CREATE SEQUENCE guiziweb_ai_platform_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE guiziweb_ai_agent_configuration_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE guiziweb_ai_agent_tool_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('ALTER TABLE guiziweb_ai_platform_configuration ALTER COLUMN id SET DEFAULT nextval(\'guiziweb_ai_platform_configuration_id_seq\')');
            $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration ALTER COLUMN id SET DEFAULT nextval(\'guiziweb_ai_agent_configuration_id_seq\')');
            $this->addSql('ALTER TABLE guiziweb_ai_agent_tool ALTER COLUMN id SET DEFAULT nextval(\'guiziweb_ai_agent_tool_id_seq\')');
        }

        // Add AUTO_INCREMENT for MySQL
        if ($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform) {
            $this->addSql('ALTER TABLE guiziweb_ai_platform_configuration MODIFY id INT AUTO_INCREMENT');
            $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration MODIFY id INT AUTO_INCREMENT');
            $this->addSql('ALTER TABLE guiziweb_ai_agent_tool MODIFY id INT AUTO_INCREMENT');
            $this->addSql('ALTER TABLE guiziweb_ai_platform_configuration DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
            $this->addSql('ALTER TABLE guiziweb_ai_agent_tool DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        }

        // Foreign keys
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration ADD CONSTRAINT FK_D5F8E5FF3F4929AD FOREIGN KEY (platform_configuration_id) REFERENCES guiziweb_ai_platform_configuration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration ADD CONSTRAINT FK_D5F8E5FF72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_tool ADD CONSTRAINT FK_98474D06DD7CCC62 FOREIGN KEY (agent_configuration_id) REFERENCES guiziweb_ai_agent_configuration (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration DROP CONSTRAINT FK_D5F8E5FF3F4929AD');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration DROP CONSTRAINT FK_D5F8E5FF72F5A1AA');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_tool DROP CONSTRAINT FK_98474D06DD7CCC62');
        $this->addSql('DROP TABLE guiziweb_ai_agent_configuration');
        $this->addSql('DROP TABLE guiziweb_ai_agent_tool');
        $this->addSql('DROP TABLE guiziweb_ai_platform_configuration');

        // Drop sequences for PostgreSQL
        if ($this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform) {
            $this->addSql('DROP SEQUENCE guiziweb_ai_platform_configuration_id_seq');
            $this->addSql('DROP SEQUENCE guiziweb_ai_agent_configuration_id_seq');
            $this->addSql('DROP SEQUENCE guiziweb_ai_agent_tool_id_seq');
        }
    }
}
