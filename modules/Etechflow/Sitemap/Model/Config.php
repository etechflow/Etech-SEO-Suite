<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Typed reader over the etechflow_sitemap/* configuration tree.
 */
class Config
{
    private const XML_PREFIX = 'etechflow_sitemap/';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(int $storeId): bool
    {
        return $this->flag('general/enabled', $storeId);
    }

    public function getPath(int $storeId): string
    {
        $path = trim((string) $this->value('general/path', $storeId));
        return $path === '' ? '/' : $path;
    }

    public function getFilename(int $storeId): string
    {
        $name = trim((string) $this->value('general/filename', $storeId));
        return $name === '' ? 'sitemap.xml' : $name;
    }

    public function getMaxUrls(int $storeId): int
    {
        $max = (int) $this->value('general/max_urls', $storeId);
        // Hard cap at the protocol limit; guard against a 0/blank misconfiguration.
        if ($max <= 0 || $max > 50000) {
            $max = 50000;
        }
        return $max;
    }

    public function isTypeEnabled(string $type, int $storeId): bool
    {
        return $this->flag($type . '/enabled', $storeId);
    }

    public function getChangefreq(string $type, int $storeId): string
    {
        return (string) ($this->value($type . '/changefreq', $storeId) ?: 'daily');
    }

    public function getPriority(string $type, int $storeId): string
    {
        return (string) ($this->value($type . '/priority', $storeId) ?: '0.5');
    }

    public function addProductImages(int $storeId): bool
    {
        return $this->flag('product/add_images', $storeId);
    }

    public function isHreflangEnabled(int $storeId): bool
    {
        return $this->flag('hreflang/enabled', $storeId);
    }

    public function isCronEnabled(): bool
    {
        return $this->flag('cron/enabled', 0);
    }

    /**
     * hreflang code for a store view, derived from its locale (en_GB -> en-gb).
     */
    public function getHreflangCode(int $storeId): string
    {
        $locale = (string) $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $storeId);
        $locale = $locale !== '' ? $locale : 'en_US';
        return strtolower(str_replace('_', '-', $locale));
    }

    /** @return string[] custom URL lines, trimmed, blanks removed */
    public function getCustomUrlLines(int $storeId): array
    {
        return $this->lines((string) $this->value('custom/urls', $storeId));
    }

    /** @return string[] excluded SKUs (lower-cased for case-insensitive match) */
    public function getExcludedSkus(int $storeId): array
    {
        return array_map('strtolower', $this->listValues((string) $this->value('exclude/product_skus', $storeId)));
    }

    /** @return string[] excluded category IDs */
    public function getExcludedCategoryIds(int $storeId): array
    {
        return $this->listValues((string) $this->value('exclude/category_ids', $storeId));
    }

    /** @return string[] excluded CMS identifiers */
    public function getExcludedCmsIdentifiers(int $storeId): array
    {
        return $this->listValues((string) $this->value('exclude/cms_identifiers', $storeId));
    }

    private function flag(string $path, int $storeId): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PREFIX . $path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    private function value(string $path, int $storeId): mixed
    {
        return $this->scopeConfig->getValue(self::XML_PREFIX . $path, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /** Split a textarea on newlines, trim, drop blanks. */
    private function lines(string $raw): array
    {
        $out = [];
        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            $line = trim($line);
            if ($line !== '') {
                $out[] = $line;
            }
        }
        return $out;
    }

    /** Split a textarea/CSV on newlines OR commas, trim, drop blanks. */
    private function listValues(string $raw): array
    {
        $out = [];
        foreach (preg_split('/[\s,]+/', $raw) as $token) {
            $token = trim($token);
            if ($token !== '') {
                $out[] = $token;
            }
        }
        return $out;
    }
}
