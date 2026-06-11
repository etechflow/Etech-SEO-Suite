<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model\Provider;

use Etechflow\Sitemap\Model\Config;
use Etechflow\Sitemap\Model\SitemapItem;
use Magento\Framework\UrlInterface;
use Magento\Sitemap\Model\ResourceModel\Catalog\CategoryFactory as SitemapCategoryResourceFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Categories, sourced from Magento's sitemap category resource model.
 */
class CategoryProvider implements ProviderInterface
{
    public function __construct(
        private readonly SitemapCategoryResourceFactory $resourceFactory,
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getType(): string
    {
        return 'category';
    }

    public function isEnabled(int $storeId): bool
    {
        return $this->config->isTypeEnabled('category', $storeId);
    }

    public function getItems(int $storeId): array
    {
        $store = $this->storeManager->getStore($storeId);
        $baseUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_LINK), '/');
        $changefreq = $this->config->getChangefreq('category', $storeId);
        $priority = $this->config->getPriority('category', $storeId);
        $excluded = array_flip($this->config->getExcludedCategoryIds($storeId));

        $items = [];
        foreach ($this->resourceFactory->create()->getCollection($storeId) as $id => $row) {
            if (isset($excluded[(string) $id])) {
                continue;
            }
            $items[] = new SitemapItem(
                key: 'category:' . $id,
                loc: $baseUrl . '/' . ltrim((string) $row->getUrl(), '/'),
                lastmod: $row->getUpdatedAt() ? (string) $row->getUpdatedAt() : null,
                changefreq: $changefreq,
                priority: $priority
            );
        }
        return $items;
    }
}
