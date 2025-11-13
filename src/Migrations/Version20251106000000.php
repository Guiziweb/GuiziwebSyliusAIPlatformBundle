<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create all AI Platform bundle tables
 */
final class Version20251106000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create AI Platform tables: platform_configuration, agent_configuration, agent_tool, vector_store_configuration';
    }

    public function up(Schema $schema): void
    {
        $this->skipIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MySQLPlatform &&
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform,
            'Migration can only be executed safely on \'mysql\' or \'postgresql\'.'
        );

        // Platform Configuration table
        $platformTable = $schema->createTable('guiziweb_ai_platform_configuration');
        $platformTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $platformTable->addColumn('code', 'string', ['length' => 100]);
        $platformTable->addColumn('name', 'string', ['length' => 255]);
        $platformTable->addColumn('enabled', 'boolean');
        $platformTable->addColumn('provider', 'string', ['length' => 50]);
        $platformTable->addColumn('apiKey', 'string', ['length' => 255, 'notnull' => false]);
        $platformTable->addColumn('settings', 'json', ['notnull' => false]);
        $platformTable->setPrimaryKey(['id']);
        $platformTable->addUniqueIndex(['code'], 'UNIQ_380D258677153098');

        // Agent Configuration table
        $agentTable = $schema->createTable('guiziweb_ai_agent_configuration');
        $agentTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $agentTable->addColumn('platform_configuration_id', 'integer');
        $agentTable->addColumn('channel_id', 'integer');
        $agentTable->addColumn('code', 'string', ['length' => 100]);
        $agentTable->addColumn('name', 'string', ['length' => 255]);
        $agentTable->addColumn('enabled', 'boolean');
        $agentTable->addColumn('model', 'string', ['length' => 100, 'notnull' => false]);
        $agentTable->addColumn('systemPrompt', 'text', ['notnull' => false]);
        $agentTable->setPrimaryKey(['id']);
        $agentTable->addIndex(['platform_configuration_id'], 'IDX_D5F8E5FF3F4929AD');
        $agentTable->addIndex(['channel_id'], 'IDX_D5F8E5FF72F5A1AA');
        $agentTable->addUniqueIndex(['code'], 'UNIQ_D5F8E5FF77153098');
        $agentTable->addForeignKeyConstraint('guiziweb_ai_platform_configuration', ['platform_configuration_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_D5F8E5FF3F4929AD');
        $agentTable->addForeignKeyConstraint('sylius_channel', ['channel_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_D5F8E5FF72F5A1AA');

        // Agent Tool table
        $toolTable = $schema->createTable('guiziweb_ai_agent_tool');
        $toolTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $toolTable->addColumn('agent_configuration_id', 'integer');
        $toolTable->addColumn('toolName', 'string', ['length' => 255]);
        $toolTable->addColumn('enabled', 'boolean');
        $toolTable->setPrimaryKey(['id']);
        $toolTable->addIndex(['agent_configuration_id'], 'IDX_98474D06DD7CCC62');
        $toolTable->addForeignKeyConstraint('guiziweb_ai_agent_configuration', ['agent_configuration_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_98474D06DD7CCC62');

        // Vector Store Configuration table
        $vectorTable = $schema->createTable('guiziweb_ai_vector_store_configuration');
        $vectorTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $vectorTable->addColumn('channel_id', 'integer');
        $vectorTable->addColumn('platform_configuration_id', 'integer');
        $vectorTable->addColumn('code', 'string', ['length' => 100]);
        $vectorTable->addColumn('name', 'string', ['length' => 255]);
        $vectorTable->addColumn('enabled', 'boolean');
        $vectorTable->addColumn('model', 'string', ['length' => 100]);
        $vectorTable->addColumn('distance_metric', 'string', ['length' => 50, 'notnull' => false]);
        $vectorTable->setPrimaryKey(['id']);
        $vectorTable->addUniqueIndex(['code'], 'UNIQ_E21A6BCB77153098');
        $vectorTable->addIndex(['channel_id'], 'IDX_E21A6BCB72F5A1AA');
        $vectorTable->addIndex(['platform_configuration_id'], 'IDX_E21A6BCB3F4929AD');
        $vectorTable->addForeignKeyConstraint('sylius_channel', ['channel_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_E21A6BCB72F5A1AA');
        $vectorTable->addForeignKeyConstraint('guiziweb_ai_platform_configuration', ['platform_configuration_id'], ['id'], ['onDelete' => 'CASCADE'], 'FK_E21A6BCB3F4929AD');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('guiziweb_ai_vector_store_configuration');
        $schema->dropTable('guiziweb_ai_agent_tool');
        $schema->dropTable('guiziweb_ai_agent_configuration');
        $schema->dropTable('guiziweb_ai_platform_configuration');
    }
}