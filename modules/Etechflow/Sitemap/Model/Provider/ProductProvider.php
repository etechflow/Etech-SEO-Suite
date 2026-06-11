<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model\Provider;

use Etechflow\Sitemap\Model\Config;
use Etechflow\Sitemap\Model\SitemapItem;
use Magento\Catalog\Model\ResourceModel\ProductFactory as CatalogProductResourceFactory;
use Magento\Framework\UrlInterface;
use Magento\Sitemap\Model\ResourceModel\Catalog\ProductFactory as SitemapProductResourceFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Products, sourced from Magento's own sitemap product resource model
 * (Magento\Sitemap\Model\ResourceModel\Catalog\Product) so URL rewrites,
 * visibility and per-store status are handled exactly as core does.
 *
 * Image entries (when enabled) rely on Magento's native
 * "sitemap/product/image_include" being set to Base or All — that is what makes
 * the resource model populate getImages(); our toggle controls emission.
 */
class ProductProvider implements ProviderInterface
{
    public function __construct(
        private readonly SitemapProductResourceFactory $resourceFactory,
        private readonly CatalogProductResourceFactory $catalogProductResourceFactory,
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getType(): string
    {
        return 'product';
    }

    public function isEnabled(int $storeId): bool
    {
        return $this->config->isTypeEnabled('product', $storeId);
    }

    public function getItems(int $storeId): array
    {
        $store = $this->storeManager->getStore($storeId);
        $baseUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_LINK), '/');
        $mediaUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA), '/');

        $changefreq = $this->config->getChangefreq('product', $storeId);
        $priority = $this->config->getPriority('product', $storeId);
        $addImages = $this->config->addProductImages($storeId);
        $excludedIds = $this->resolveExcludedIds($storeId);

        $items = [];
        foreach ($this->resourceFactory->create()->getCollection($storeId) as $id => $row) {
            if (isset($excludedIds[(int) $id])) {
                continue;
            }
            $images = $addImages ? $this->extractImages($row, $mediaUrl) : [];
            $items[] = new SitemapItem(
                key: 'product:' . $id,
                loc: $baseUrl . '/' . ltrim((string) $row->getUrl(), '/'),
                lastmod: $row->getUpdatedAt() ? (string) $row->getUpdatedAt() : null,
                changefreq: $changefreq,
                priority: $priority,
                images: $images
            );
        }
        return $items;
    }

    /**
     * Map excluded SKUs -> entity IDs (the sitemap resource model exposes IDs, not SKUs).
     *
     * @return array<int,true>
     */
    private function resolveExcludedIds(int $storeId): array
    {
        $skus = $this->config->getExcludedSkus($storeId);
        if (!$skus) {
            return [];
        }
        // getProductsIdsBySkus is case-sensitive on the stored SKU; pass the raw
        // (un-lowercased) list too by reading config values directly is overkill —
        // we simply look up the lowercased set against a fetched map.
        $resource = $this->catalogProductResourceFactory->create();
        $map = $resource->getProductsIdsBySkus($skus); // [sku => id]
        $ids = [];
        foreach ($map as $id) {
            $ids[(int) $id] = true;
        }
        return $ids;
    }

    /**
     * @return array<int,array{loc:string,title:?string}>
     */
    private function extractImages(\Magento\Framework\DataObject $row, string $mediaUrl): array
    {
        $imagesData = $row->getImages();
        if (!$imagesData) {
            return [];
        }
        $collection = $imagesData->getCollection();
        if (!is_array($collection) && !($collection instanceof \Traversable)) {
            return [];
        }
        $title = $imagesData->getTitle() ? (string) $imagesData->getTitle() : null;
        $out = [];
        foreach ($collection as $image) {
            $path = (string) $image->getUrl();
            if ($path === '') {
                continue;
            }
            $loc = preg_match('#^https?://#i', $path)
                ? $path
                : $mediaUrl . '/' . ltrim($path, '/');
            $out[] = ['loc' => $loc, 'title' => $title];
        }
        return $out;
    }
}
