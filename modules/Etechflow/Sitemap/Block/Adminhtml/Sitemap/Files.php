<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Block\Adminhtml\Sitemap;

use Etechflow\Sitemap\Model\Config;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Lists the sitemap files currently on disk for the default store view, with a
 * public link and a "Generate Now" action.
 */
class Files extends Template
{
    public function __construct(
        Context $context,
        private readonly Filesystem $filesystem,
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager,
        private readonly FormKey $formKey,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getGenerateUrl(): string
    {
        return $this->getUrl('etechflow_sitemap/sitemap/generate');
    }

    public function getFormKey(): string
    {
        return $this->formKey->getFormKey();
    }

    public function isEnabled(): bool
    {
        $store = $this->storeManager->getDefaultStoreView();
        return $store !== null && $this->config->isEnabled((int) $store->getId());
    }

    /**
     * @return array<int,array{name:string,size:string,modified:string,url:string}>
     */
    public function getFiles(): array
    {
        $store = $this->storeManager->getDefaultStoreView();
        if (!$store) {
            return [];
        }
        $storeId = (int) $store->getId();
        $relDir = trim($this->config->getPath($storeId), '/');
        $baseUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_LINK, $store->isFrontUrlSecure()), '/');

        $read = $this->filesystem->getDirectoryRead(DirectoryList::PUB);
        if ($relDir !== '' && !$read->isExist($relDir)) {
            return [];
        }

        $out = [];
        foreach ($read->read($relDir ?: null) as $entry) {
            $name = basename($entry);
            if (!preg_match('/\.xml$/i', $name) || stripos($name, 'sitemap') === false) {
                continue;
            }
            $rel = $relDir === '' ? $name : $relDir . '/' . $name;
            try {
                $stat = $read->stat($rel);
            } catch (\Throwable $e) {
                continue;
            }
            $out[] = [
                'name' => $name,
                'size' => $this->humanSize((int) ($stat['size'] ?? 0)),
                'modified' => !empty($stat['mtime']) ? date('Y-m-d H:i', (int) $stat['mtime']) : '-',
                'url' => $baseUrl . '/' . ($relDir !== '' ? $relDir . '/' : '') . $name,
            ];
        }

        usort($out, static fn ($a, $b) => strcmp($a['name'], $b['name']));
        return $out;
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        $units = ['KB', 'MB', 'GB'];
        $value = $bytes / 1024;
        $i = 0;
        while ($value >= 1024 && $i < count($units) - 1) {
            $value /= 1024;
            $i++;
        }
        return round($value, 1) . ' ' . $units[$i];
    }
}
