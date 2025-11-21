# Guiziweb Sylius AI Platform Bundle

![Build](https://github.com/Guiziweb/GuiziwebSyliusAIPlatformBundle/workflows/CI/badge.svg)
![Packagist Version](https://img.shields.io/packagist/v/guiziweb/sylius-ai-platform-bundle)
![PHP](https://img.shields.io/packagist/php-v/guiziweb/sylius-ai-platform-bundle)
![License](https://img.shields.io/github/license/Guiziweb/GuiziwebSyliusAIPlatformBundle)
![Status](https://img.shields.io/badge/status-complete-green)

Sylius admin bundle providing a complete back-office interface for Symfony AI Platform.

## Description

The Guiziweb Sylius AI Platform Bundle provides a centralized administration interface for managing AI configurations in your Sylius store. It serves as the foundation for the Guiziweb AI ecosystem, handling API keys, agent configurations, vector store setups, and model selection across all channels.

This bundle acts as the single source of truth for AI configuration - other plugins in the ecosystem reference its configurations rather than storing their own credentials.

Part of the [Guiziweb Sylius AI Ecosystem](https://guiziweb.github.io).

## Features

- **Platform Configuration Management**: Centralized AI platform credentials and settings
- **Multi-Provider Support**: OpenAI, Anthropic, Mistral, Gemini, Ollama, Azure OpenAI, Cerebras, DeepSeek, LM Studio, and more (23+ providers)
- **Multiple Configurations Per Provider**: Separate dev/prod API keys
- **Agent Configuration Management**: Channel-specific agent configurations with custom prompts
- **Model Selection Per Agent**: Dynamic model loading based on platform
- **Tool Management**: Configurable tool assignments per agent
- **Multi-Channel Support**: Different AI configurations per Sylius sales channel
- **Enable/Disable Controls**: Activate/deactivate platforms and agents without deletion

## Requirements

| Dependency | Version | Notes |
| ---------- | ------- | ----- |
| PHP | 8.2+ | |
| Sylius | 2.0+ | |
| Symfony | 7.3+ | |
| symfony/ai-platform | @dev | Core AI platform |
| symfony/ai-agent | @dev | Agent and tools |
| symfony/ai-store | @dev | Vector stores |

## Installation

1. **Add Guiziweb Flex recipes endpoint** to your `composer.json`:

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

   **Note:** Set `"minimum-stability": "dev"` and `"prefer-stable": true` until stable release.

2. **Require the package via Composer:**

   ```bash
   composer require guiziweb/sylius-ai-platform-bundle
   ```

   The Symfony Flex recipe will automatically:
   - Register the bundle in `config/bundles.php`
   - Create configuration file in `config/packages/guiziweb_sylius_ai_platform.yaml`
   - Create routes file in `config/routes/guiziweb_sylius_ai_platform.yaml`

3. **Run database migrations:**

   ```bash
   php bin/console doctrine:migrations:migrate -n
   ```

## Configuration

### Platform Configuration

Platform configurations are global and not tied to specific channels.

**Fields:**

- **Code**: Unique identifier (e.g., `openai_prod`, `claude_main`)
- **Name**: Human-readable name
- **Provider**: AI platform provider
- **API Key**: Authentication credentials
- **Enabled**: Activate/deactivate the platform

### Agent Configuration

Agent configurations are channel-specific.

**Fields:**

- **Code**: Unique identifier
- **Name**: Agent name
- **Channel**: Sylius channel assignment
- **Platform Configuration**: Reference to platform credentials
- **Model**: Specific model to use (e.g., `gpt-4`, `claude-3-sonnet`)
- **System Prompt**: Instructions defining agent behavior
- **Tools**: Assignable capabilities
- **Enabled**: Activate/deactivate the agent

## Usage

### Creating a Platform Configuration

1. Navigate to **AI Configuration > Platform Configurations** in the admin panel
2. Click **Create**
3. Fill in the required fields:
   - Code: `openai_prod`
   - Name: `OpenAI Production`
   - Provider: Select `OpenAI`
   - API Key: Enter your API key
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

### Adding Custom Tools

Tools are provided by Symfony AI Platform using the `#[AsTool]` attribute:

```php
namespace App\AI\Tool;

use Symfony\AI\Agent\Toolbox\Attribute\AsTool;

#[AsTool(description: 'Search product catalog')]
class ProductSearchTool
{
    public function search(string $query): array
    {
        // Your implementation
    }
}
```

## Testing

```bash
# PHPUnit
vendor/bin/phpunit

# Behat
vendor/bin/behat

# Static analysis
vendor/bin/phpstan analyse

# Coding standards
vendor/bin/ecs check
```

## Contributing

Contributions are welcome! Please read our [Contributing Guide](CONTRIBUTING.md) before submitting a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Related Plugins

- [Guiziweb Sylius Shopping Assistant Plugin](https://github.com/Guiziweb/GuiziwebSyliusShoppingAssistantPlugin) - AI-powered chat widget for Sylius storefronts
- [Guiziweb Sylius Semantic Search Plugin](https://github.com/Guiziweb/GuiziwebSyliusSemanticSearchPlugin) - Vector-based semantic search for Sylius products

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
