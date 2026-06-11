<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model\Provider;

use Etechflow\Sitemap\Model\Config;
use Etechflow\Sitemap\Model\SitemapItem;
use Magento\Framework\UrlInterface;
use Magento\Sitemap\Model\ResourceModel\Cms\PageFactory as SitemapCmsResourceFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * CMS pages, sourced from Magento's sitemap CMS resource model.
 */
class CmsProvider implements ProviderInterface
{
    public function __construct(
        private readonly SitemapCmsResourceFactory $resourceFactory,
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getType(): string
    {
        return 'cms';
    }

    public function isEnabled(int $storeId): bool
    {
        return $this->config->isTypeEnabled('cms', $storeId);
    }

    public function getItems(int $storeId): array
    {
        $store = $this->storeManager->getStore($storeId);
        $baseUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_LINK), '/');
        $changefreq = $this->config->getChangefreq('cms', $storeId);
        $priority = $this->config->getPriority('cms', $storeId);
        $excluded = array_flip($this->config->getExcludedCmsIdentifiers($storeId));

        $items = [];
        foreach ($this->resourceFactory->create()->getCollection($storeId) as $id => $row) {
            $url = (string) $row->getUrl();
            // The resource model's url is the CMS identifier (request path) — match excludes on it.
            if (isset($excluded[$url]) || isset($excluded[ltrim($url, '/')])) {
                continue;
            }
            $key = $row->getId() ? 'cms:' . $row->getId() : 'cms:' . $url;
            $items[] = new SitemapItem(
                key: $key,
                loc: $baseUrl . '/' . ltrim($url, '/'),
                lastmod: $row->getUpdatedAt() ? (string) $row->getUpdatedAt() : null,
                changefreq: $changefreq,
                priority: $priority
            );
        }
        return $items;
    }
}
