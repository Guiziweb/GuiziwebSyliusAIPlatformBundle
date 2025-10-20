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
        $this->addSql('CREATE TABLE guiziweb_ai_agent_configuration (id INT AUTO_INCREMENT NOT NULL, platform_configuration_id INT NOT NULL, channel_id INT NOT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, model VARCHAR(100) DEFAULT NULL, systemPrompt LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_D5F8E5FF77153098 (code), INDEX IDX_D5F8E5FF3F4929AD (platform_configuration_id), INDEX IDX_D5F8E5FF72F5A1AA (channel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guiziweb_ai_agent_tool (id INT AUTO_INCREMENT NOT NULL, agent_configuration_id INT NOT NULL, toolName VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, INDEX IDX_98474D06DD7CCC62 (agent_configuration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE guiziweb_ai_platform_configuration (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, provider VARCHAR(50) NOT NULL, apiKey VARCHAR(255) DEFAULT NULL, settings JSON DEFAULT NULL, UNIQUE INDEX UNIQ_380D258677153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration ADD CONSTRAINT FK_D5F8E5FF3F4929AD FOREIGN KEY (platform_configuration_id) REFERENCES guiziweb_ai_platform_configuration (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration ADD CONSTRAINT FK_D5F8E5FF72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_tool ADD CONSTRAINT FK_98474D06DD7CCC62 FOREIGN KEY (agent_configuration_id) REFERENCES guiziweb_ai_agent_configuration (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration DROP FOREIGN KEY FK_D5F8E5FF3F4929AD');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_configuration DROP FOREIGN KEY FK_D5F8E5FF72F5A1AA');
        $this->addSql('ALTER TABLE guiziweb_ai_agent_tool DROP FOREIGN KEY FK_98474D06DD7CCC62');
        $this->addSql('DROP TABLE guiziweb_ai_agent_configuration');
        $this->addSql('DROP TABLE guiziweb_ai_agent_tool');
        $this->addSql('DROP TABLE guiziweb_ai_platform_configuration');
    }
}
