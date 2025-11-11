<?php

declare(strict_types=1);

namespace Guiziweb\SyliusAIPlatformBundle\Store;

use Guiziweb\SyliusAIPlatformBundle\Provider\VectorStoreProvider;
use Guiziweb\SyliusAIPlatformBundle\Provider\VectorizerProvider;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\AI\Platform\Vector\VectorInterface;
use Symfony\AI\Store\Document\Metadata;
use Symfony\AI\Store\Document\VectorDocument;
use Symfony\Component\Uid\Uuid;

/**
 * Indexes Sylius product variants into a vector store for semantic search.
 *
 * @author Camille Islasse
 */
final readonly class ProductVariantIndexer
{
    public function __construct(
        private RepositoryInterface $productVariantRepository,
        private VectorizerProvider $vectorizerProvider,
        private VectorStoreProvider $storeProvider,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * Index a single product variant for a specific channel.
     */
    public function indexVariant(ProductVariantInterface $variant, ChannelInterface $channel): void
    {
        $store = $this->storeProvider->getStoreForChannel($channel);
        $document = $this->createVectorDocument($variant, $channel);

        if ($document instanceof VectorDocument) {
            $store->add($document);
        }
    }

    /**
     * Index all enabled product variants for a specific channel.
     *
     * @return int Number of variants indexed
     */
    public function indexAllVariants(ChannelInterface $channel, int $batchSize = 50): int
    {
        if ($batchSize <= 0) {
            throw new \InvalidArgumentException('Batch size must be greater than 0.');
        }

        $store = $this->storeProvider->getStoreForChannel($channel);
        $variants = $this->productVariantRepository->findAll();

        if (!is_array($variants)) {
            throw new \RuntimeException('Variant repository must return an array.');
        }

        $count = 0;
        $batch = [];

        foreach ($variants as $variant) {
            if (!$variant instanceof ProductVariantInterface) {
                continue;
            }

            $document = $this->createVectorDocument($variant, $channel);

            if (!$document instanceof VectorDocument) {
                continue;
            }

            $batch[] = $document;

            if (count($batch) >= $batchSize) {
                $store->add(...$batch);
                $count += count($batch);
                $batch = [];
            }
        }

        // Add remaining documents
        if (count($batch) > 0) {
            $store->add(...$batch);
            $count += count($batch);
        }

        return $count;
    }

    /**
     * Search product variants by query text for a specific channel.
     *
     * @return array<int, VectorDocument>
     */
    public function searchVariants(string $query, ChannelInterface $channel, int $limit = 10): array
    {
        if ($limit <= 0) {
            throw new \InvalidArgumentException('Limit must be greater than 0.');
        }

        if ('' === trim($query)) {
            return [];
        }

        $store = $this->storeProvider->getStoreForChannel($channel);
        $vectorizer = $this->vectorizerProvider->getVectorizerForChannel($channel);

        $queryEmbedding = $vectorizer->vectorize($query);

        if (!$queryEmbedding instanceof VectorInterface) {
            throw new \RuntimeException('Vectorizer must return a VectorInterface.');
        }

        $results = $store->query($queryEmbedding, ['maxItems' => $limit]);

        if (!is_array($results)) {
            throw new \RuntimeException('Store query must return an array.');
        }

        // Calculate and add similarity scores (CacheStore doesn't return them)
        $resultsWithScores = [];
        foreach ($results as $result) {
            if (!$result instanceof VectorDocument) {
                continue;
            }

            $score = $this->calculateCosineSimilarity($queryEmbedding, $result->vector);

            // Create new VectorDocument with score
            $resultsWithScores[] = new VectorDocument(
                id: $result->id,
                vector: $result->vector,
                metadata: $result->metadata,
                score: $score,
            );
        }

        return $resultsWithScores;
    }

    /**
     * Create a vector document from a variant for a specific channel.
     * Returns null if the variant cannot be indexed (and logs the reason).
     */
    private function createVectorDocument(
        ProductVariantInterface $variant,
        ChannelInterface $channel
    ): ?VectorDocument {
        // Check variant is enabled
        if (!$variant->isEnabled()) {
            $this->logger->info('Skipping disabled variant', [
                'variant_code' => $variant->getCode(),
            ]);

            return null;
        }

        // Get product
        $product = $variant->getProduct();

        if (!$product instanceof ProductInterface) {
            $this->logger->warning('Variant has no valid product', [
                'variant_code' => $variant->getCode(),
            ]);

            return null;
        }

        // Check product is enabled
        if (!$product->isEnabled()) {
            $this->logger->info('Skipping variant of disabled product', [
                'variant_code' => $variant->getCode(),
                'product_code' => $product->getCode(),
            ]);

            return null;
        }

        // Check product is available on channel
        if (!$product->hasChannel($channel)) {
            $this->logger->info('Product not available on channel', [
                'variant_code' => $variant->getCode(),
                'product_code' => $product->getCode(),
                'channel_code' => $channel->getCode(),
            ]);

            return null;
        }

        // Get locale from channel
        $defaultLocale = $channel->getDefaultLocale();
        if (!$defaultLocale instanceof LocaleInterface) {
            $this->logger->error('Channel has no default locale', [
                'channel_code' => $channel->getCode(),
            ]);

            return null;
        }

        $localeCode = $defaultLocale->getCode();
        if (null === $localeCode || '' === trim($localeCode)) {
            $this->logger->error('Channel locale code is empty', [
                'channel_code' => $channel->getCode(),
            ]);

            return null;
        }

        // Get product translation
        $translation = $product->getTranslation($localeCode);

        if (!$translation instanceof ProductTranslationInterface) {
            $this->logger->warning('Product translation not found', [
                'product_code' => $product->getCode(),
                'locale' => $localeCode,
            ]);

            return null;
        }

        $name = $translation->getName();
        $description = $translation->getDescription();
        $slug = $translation->getSlug();

        if (null === $name || '' === trim($name)) {
            $this->logger->warning('Product name is empty', [
                'product_code' => $product->getCode(),
                'locale' => $localeCode,
            ]);

            return null;
        }

        $variantCode = $variant->getCode();

        if (null === $variantCode || '' === trim($variantCode)) {
            $this->logger->warning('Variant code is empty', [
                'variant_id' => $variant->getId(),
            ]);

            return null;
        }

        // Build searchable text
        $optionsText = $this->buildVariantOptionsText($variant);

        $text = sprintf(
            "%s\n%s\n%s\n%s",
            $name,
            $description ?? '',
            $slug ?? '',
            $optionsText,
        );

        // Generate embedding using configured vectorizer
        $vectorizer = $this->vectorizerProvider->getVectorizerForChannel($channel);
        $embedding = $vectorizer->vectorize($text);

        if (!$embedding instanceof VectorInterface) {
            $this->logger->error('Vectorizer did not return a VectorInterface', [
                'variant_code' => $variantCode,
            ]);

            return null;
        }

        // Create and return vector document
        // Generate a deterministic UUID from the variant code using UUID v5
        $documentId = Uuid::v5(Uuid::fromString(Uuid::NAMESPACE_URL), 'product-variant:' . $variantCode);

        // Get product attributes
        $brand = null;
        $material = null;
        if ($product->hasAttribute('t_shirt_brand')) {
            $brandAttribute = $product->getAttributeByCodeAndLocale('t_shirt_brand', $localeCode);
            if (null !== $brandAttribute) {
                $brand = $brandAttribute->getValue();
            }
        }
        if ($product->hasAttribute('t_shirt_material')) {
            $materialAttribute = $product->getAttributeByCodeAndLocale('t_shirt_material', $localeCode);
            if (null !== $materialAttribute) {
                $material = $materialAttribute->getValue();
            }
        }

        return new VectorDocument(
            id: $documentId,
            vector: $embedding,
            metadata: new Metadata([
                'channel_code' => $channel->getCode(),
                'product_id' => $product->getId(),
                'product_code' => $product->getCode(),
                'product_name' => $name,
                'product_slug' => $slug,
                'short_description' => $translation->getShortDescription(),
                'variant_id' => $variant->getId(),
                'variant_code' => $variantCode,
                'variant_name' => $variant->getName(),
                'options' => $this->extractVariantOptions($variant),
                'enabled' => $variant->isEnabled(),
                'on_hand' => $variant->getOnHand(),
                'locale' => $localeCode,
                'brand' => $brand,
                'material' => $material,
            ]),
        );
    }

    /**
     * Build human-readable text from variant options.
     */
    private function buildVariantOptionsText(ProductVariantInterface $variant): string
    {
        $optionValues = $variant->getOptionValues();

        if (null === $optionValues || 0 === $optionValues->count()) {
            return '';
        }

        $parts = [];
        foreach ($optionValues as $optionValue) {
            if (!$optionValue instanceof ProductOptionValueInterface) {
                continue;
            }

            $option = $optionValue->getOption();
            if (null === $option) {
                continue;
            }

            $optionTranslation = $option->getTranslation();
            $valueTranslation = $optionValue->getTranslation();

            if (null !== $optionTranslation && null !== $valueTranslation) {
                $parts[] = sprintf(
                    '%s: %s',
                    $optionTranslation->getName() ?? '',
                    $valueTranslation->getValue() ?? ''
                );
            }
        }

        return implode(', ', $parts);
    }

    /**
     * Extract variant options as structured data.
     *
     * @return array<string, string>
     */
    private function extractVariantOptions(ProductVariantInterface $variant): array
    {
        $optionValues = $variant->getOptionValues();

        if (null === $optionValues || 0 === $optionValues->count()) {
            return [];
        }

        $options = [];
        foreach ($optionValues as $optionValue) {
            if (!$optionValue instanceof ProductOptionValueInterface) {
                continue;
            }

            $option = $optionValue->getOption();
            if (null === $option) {
                continue;
            }

            $optionCode = $option->getCode();
            $valueCode = $optionValue->getCode();

            if (null !== $optionCode && null !== $valueCode) {
                $options[$optionCode] = $valueCode;
            }
        }

        return $options;
    }

    /**
     * Calculate cosine similarity between two vectors.
     * Returns a score between 0 and 1 (1 = identical, 0 = opposite).
     */
    private function calculateCosineSimilarity(VectorInterface $vector1, VectorInterface $vector2): float
    {
        $values1 = $vector1->getData();
        $values2 = $vector2->getData();

        if (count($values1) !== count($values2)) {
            throw new \InvalidArgumentException('Vectors must have the same dimensions');
        }

        $dotProduct = 0.0;
        $magnitude1 = 0.0;
        $magnitude2 = 0.0;

        for ($i = 0; $i < count($values1); ++$i) {
            $dotProduct += $values1[$i] * $values2[$i];
            $magnitude1 += $values1[$i] ** 2;
            $magnitude2 += $values2[$i] ** 2;
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }
}
