<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model\Provider;

use Etechflow\Sitemap\Model\Config;
use Etechflow\Sitemap\Model\SitemapItem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Operator-defined additional URLs from configuration.
 * Each line: "path" or "path|priority|changefreq".
 */
class CustomProvider implements ProviderInterface
{
    private const VALID_CHANGEFREQ = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];

    public function __construct(
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getType(): string
    {
        return 'custom';
    }

    public function isEnabled(int $storeId): bool
    {
        // Always "enabled"; it simply yields nothing when no lines are configured.
        return true;
    }

    public function getItems(int $storeId): array
    {
        $lines = $this->config->getCustomUrlLines($storeId);
        if (!$lines) {
            return [];
        }
        $store = $this->storeManager->getStore($storeId);
        $baseUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_LINK, $store->isFrontUrlSecure()), '/');

        $items = [];
        foreach ($lines as $line) {
            $parts = array_map('trim', explode('|', $line));
            $path = $parts[0] ?? '';
            if ($path === '') {
                continue;
            }
            $priority = isset($parts[1]) && is_numeric($parts[1]) ? $parts[1] : '0.5';
            $changefreq = isset($parts[2]) && in_array($parts[2], self::VALID_CHANGEFREQ, true)
                ? $parts[2]
                : 'monthly';

            $loc = preg_match('#^https?://#i', $path)
                ? $path
                : $baseUrl . '/' . ltrim($path, '/');

            $items[] = new SitemapItem(
                key: 'custom:' . md5($loc),
                loc: $loc,
                lastmod: null,
                changefreq: $changefreq,
                priority: $priority
            );
        }
        return $items;
    }
}
