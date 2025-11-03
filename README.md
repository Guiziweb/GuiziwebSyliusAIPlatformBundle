# Guiziweb Sylius AI Platform Bundle

A Sylius admin interface for managing [Symfony AI Platform](https://github.com/symfony-ai/symfony-ai) configurations. This bundle provides the back-office (BO) layer for Sylius e-commerce applications to configure AI platforms and agents.

## Overview

This plugin is built on top of **Symfony AI Platform** and provides a user-friendly Sylius admin interface for managing AI configurations. It does not implement AI functionality itself - it simply provides the administrative layer to:

- Store and manage AI platform credentials (API keys, provider settings)
- Configure AI agents with specific models, prompts, and tools
- Assign agents to Sylius channels

The actual AI capabilities are provided by Symfony AI Platform, which handles the communication with AI providers (OpenAI, Anthropic, etc.).

## Features

### Platform Configuration Management
- Centralized AI platform credentials and settings
- Support for multiple AI providers (OpenAI, Anthropic, Mistral, Gemini, Ollama, and more)
- Multiple configurations per provider (e.g., separate dev/prod API keys)
- Enable/disable platforms without deleting configurations

### Agent Configuration Management
- Channel-specific agent configurations
- Custom system prompts per agent
- Model selection per agent
- Configurable tool assignments
- Enable/disable agents per channel

### Supported AI Providers

This bundle provides admin interfaces for all AI providers supported by **Symfony AI Platform**, including:

- **OpenAI** (GPT-4, GPT-3.5)
- **Anthropic** (Claude)
- **Mistral AI**
- **Google Gemini**
- **Ollama** (local models)
- **Azure OpenAI**
- **Cerebras**
- **DeepSeek**
- **LM Studio** (local)
- And many more...

The actual communication with these providers is handled by Symfony AI Platform.

## Requirements

- PHP 8.2 or higher
- Sylius 2.0 or higher
- Symfony 7.3 or higher
- **Symfony AI Platform** (installed automatically as a dependency)

## Installation

### Quick Installation (Recommended)

The bundle uses **Symfony Flex** for automatic configuration:

```bash
composer require guiziweb/sylius-ai-platform-bundle
```

This will automatically:
- Register the bundle in `config/bundles.php`
- Create configuration file in `config/packages/guiziweb_sylius_ai_platform.yaml`
- Create routes file in `config/routes/guiziweb_sylius_ai_platform.yaml`

### Configuration Requirements

To enable Symfony Flex recipes from this repository, add the custom recipe endpoint to your `composer.json`:

```json
{
    "extra": {
        "symfony": {
            "allow-contrib": true,
            "endpoint": [
                "https://api.github.com/repos/Guiziweb/SyliusRecipes/contents/index.json?ref=flex/main",
                "https://api.github.com/repos/Sylius/SyliusRecipes/contents/index.json?ref=flex/main",
                "flex://defaults"
            ]
        }
    }
}
```

**Note:** You also need to set `"minimum-stability": "dev"` and `"prefer-stable": true` in your `composer.json` until a stable release is tagged.

### Post-Installation

After installation, run the database migrations:

```bash
php bin/console doctrine:migrations:migrate -n
```

### Manual Installation (Alternative)

If you prefer manual installation or are setting up a Sylius plugin test application:

1. **Install via Composer:**
   ```bash
   composer require guiziweb/sylius-ai-platform-bundle:dev-main
   ```

2. **Enable the bundle** in `config/bundles.php`:
   ```php
   return [
       // ... other bundles
       Guiziweb\SyliusAIPlatformBundle\GuiziwebSyliusAIPlatformBundle::class => ['all' => true],
   ];
   ```

3. **Import routes** in `config/routes.yaml`:
   ```yaml
   guiziweb_sylius_ai_platform:
       resource: "@GuiziwebSyliusAIPlatformBundle/config/routes.yaml"
   ```

4. **Import configuration** - create `config/packages/guiziweb_sylius_ai_platform.yaml`:
   ```yaml
   imports:
       - { resource: "@GuiziwebSyliusAIPlatformBundle/config/config.yaml" }
   ```

5. **Run migrations:**
   ```bash
   php bin/console doctrine:migrations:migrate -n
   ```

## Configuration

### Platform Configuration

Platform configurations are global and not tied to specific channels. This allows sharing API keys across multiple agents and channels.

**Fields:**
- **Code**: Unique identifier (e.g., `openai_prod`, `claude_main`)
- **Name**: Human-readable name (e.g., "OpenAI Production", "Claude Main")
- **Provider**: AI platform provider (OpenAI, Anthropic, etc.)
- **API Key**: Authentication credentials
- **Enabled**: Activate/deactivate the platform

### Agent Configuration

Agent configurations are channel-specific, allowing different AI behaviors per sales channel.

**Fields:**
- **Code**: Unique identifier
- **Name**: Agent name
- **Channel**: Sylius channel assignment
- **Platform Configuration**: Reference to platform credentials
- **Model**: Specific model to use (e.g., `gpt-4`, `claude-3-sonnet`)
- **System Prompt**: Instructions defining agent behavior
- **Tools**: Assignable capabilities (extensible via plugins)
- **Enabled**: Activate/deactivate the agent

## Usage

### Creating a Platform Configuration

1. Navigate to **AI Configuration > Platform Configurations** in the admin panel
2. Click **Create**
3. Fill in the required fields:
   - Code: `openai_prod`
   - Name: `OpenAI Production`
   - Provider: Select `OpenAI`
   - API Key: Enter your OpenAI API key
4. Save

### Creating an Agent Configuration

1. Navigate to **AI Configuration > Agent Configurations**
2. Click **Create**
3. Configure the agent:
   - Code: `support_bot_en`
   - Name: `Customer Support Bot (EN)`
   - Channel: Select your sales channel
   - Platform Configuration: Select `openai_prod`
   - Model: Select `gpt-4` (dynamically loaded based on platform)
   - System Prompt: Define the agent's behavior
4. Save

## Architecture

This bundle provides the **back-office layer** only. It stores configuration in the database and provides admin CRUD interfaces. The actual AI functionality is delegated to **Symfony AI Platform**.

### Entities

**PlatformConfiguration**
- Stores AI platform credentials (API keys, provider selection)
- Global scope (no channel binding)
- Supports multiple instances per provider (e.g., separate dev/prod keys)

**AgentConfiguration**
- Stores AI agent configuration (model, system prompt, tools)
- Channel-specific (allows different agents per Sylius channel)
- References a PlatformConfiguration for credentials
- Can have multiple tools assigned

**AgentTool**
- Represents an assignable capability from Symfony AI Platform
- Can be enabled/disabled per agent
- Extensible through Symfony service tagging

### Relationship with Symfony AI Platform

This bundle acts as a **configuration management layer** for Symfony AI Platform:

1. **Admin stores configuration** - Users configure platforms and agents via Sylius admin
2. **Bundle stores in database** - Configurations are persisted in Doctrine entities
3. **Symfony AI Platform executes** - Your application code uses Symfony AI Platform with these configurations
4. **Bundle provides the glue** - Factories and services bridge Sylius configuration to Symfony AI Platform instances

## Extension Points

### Adding Custom Tools

Tools are provided by **Symfony AI Platform** using the `#[AsTool]` attribute. This bundle simply exposes them in the admin interface for assignment to agents.

Create a tool using Symfony AI Platform conventions:

```php
namespace App\AI\Tool;

use Symfony\AI\Agent\Toolbox\Attribute\AsTool;

#[AsTool(description: 'Search product catalog')]
class ProductSearchTool
{
    public function search(string $query): array
    {
        // Your implementation
        // This tool will be automatically discovered by Symfony AI Platform
        // and made available in this bundle's admin interface
    }
}
```

### Custom Providers

AI providers are handled by **Symfony AI Platform**. This bundle provides an `AiProvider` enum listing available providers for the admin interface. If Symfony AI Platform adds new providers, you can extend the enum accordingly.

## Development

### Running Tests

```bash
# PHPUnit
vendor/bin/phpunit

# Behat
vendor/bin/behat

# Code Quality
vendor/bin/phpstan analyse
vendor/bin/ecs check
```

### Docker Environment

```bash
# Initialize
make init

# Database setup
make database-init

# Load fixtures
make load-fixtures

# Run tests
make phpunit
make behat
```

## License

This bundle is released under the MIT License. See the bundled LICENSE file for details.

## Credits

Developed by Guiziweb for the Sylius e-commerce platform.

## Support

For issues and feature requests, please use the GitHub issue tracker.
