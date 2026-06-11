<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model;

use Etechflow\Sitemap\Model\Provider\ProviderInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Builds the sitemap files for every enabled store view: gathers items from the
 * configured providers, attaches hreflang alternates within each website, splits
 * oversized sets and writes a sitemap index when needed.
 */
class Generator
{
    /**
     * @param ProviderInterface[] $providers
     */
    public function __construct(
        private readonly Config $config,
        private readonly Writer $writer,
        private readonly StoreManagerInterface $storeManager,
        private readonly Filesystem $filesystem,
        private readonly LoggerInterface $logger,
        private readonly array $providers = []
    ) {
    }

    /**
     * @return array{stores:array<int,array{files:string[],urls:int}>,total_urls:int,files:string[]}
     */
    public function generate(): array
    {
        $result = ['stores' => [], 'total_urls' => 0, 'files' => []];
        $defaultStoreView = $this->storeManager->getDefaultStoreView();
        $defaultStoreId = $defaultStoreView ? (int) $defaultStoreView->getId() : 0;
        $pub = $this->filesystem->getDirectoryWrite(DirectoryList::PUB);

        foreach ($this->storeManager->getWebsites() as $website) {
            /** @var array<int,SitemapItem[]> $storeItems */
            $storeItems = [];
            foreach ($website->getStores() as $store) {
                $storeId = (int) $store->getId();
                if (!$store->getIsActive() || !$this->config->isEnabled($storeId)) {
                    continue;
                }
                $storeItems[$storeId] = $this->collectItems($storeId);
            }
            if (!$storeItems) {
                continue;
            }

            $crossMap = $this->buildHreflangMap($storeItems);

            foreach ($storeItems as $storeId => $items) {
                if (count($storeItems) > 1 && $this->config->isHreflangEnabled($storeId)) {
                    foreach ($items as $item) {
                        if (isset($crossMap[$item->key])) {
                            $item->alternates = $crossMap[$item->key];
                        }
                    }
                }
                $written = $this->writeStore($pub, $storeId, $items, $defaultStoreId);
                $result['stores'][$storeId] = ['files' => $written, 'urls' => count($items)];
                $result['total_urls'] += count($items);
                $result['files'] = array_merge($result['files'], $written);
            }
        }

        return $result;
    }

    /**
     * @return SitemapItem[]
     */
    private function collectItems(int $storeId): array
    {
        $all = [];
        foreach ($this->providers as $provider) {
            if (!$provider->isEnabled($storeId)) {
                continue;
            }
            try {
                foreach ($provider->getItems($storeId) as $item) {
                    $all[] = $item;
                }
            } catch (\Throwable $e) {
                // A failing provider must not abort the whole sitemap.
                $this->logger->error(sprintf(
                    'Etechflow_Sitemap: provider "%s" failed for store %d: %s',
                    $provider->getType(),
                    $storeId,
                    $e->getMessage()
                ));
            }
        }
        return $all;
    }

    /**
     * @param array<int,SitemapItem[]> $storeItems
     * @return array<string,array<string,string>> key => [hreflang => loc]
     */
    private function buildHreflangMap(array $storeItems): array
    {
        $map = [];
        foreach ($storeItems as $storeId => $items) {
            $code = $this->config->getHreflangCode($storeId);
            foreach ($items as $item) {
                $map[$item->key][$code] = $item->loc;
            }
        }
        return $map;
    }

    /**
     * @param SitemapItem[] $items
     * @return string[] relative file paths written
     */
    private function writeStore($pub, int $storeId, array $items, int $defaultStoreId): array
    {
        $store = $this->storeManager->getStore($storeId);
        $relDir = trim($this->config->getPath($storeId), '/');
        $filename = $this->config->getFilename($storeId);
        $maxUrls = $this->config->getMaxUrls($storeId);
        $baseUrl = rtrim($store->getBaseUrl(UrlInterface::URL_TYPE_LINK, $store->isFrontUrlSecure()), '/');

        $stem = preg_replace('/\.xml$/i', '', $filename) ?: 'sitemap';
        $perStem = ($storeId === $defaultStoreId) ? $stem : $stem . '-' . $store->getCode();

        $chunks = array_chunk($items, $maxUrls);
        $written = [];

        if (count($chunks) <= 1) {
            $name = $perStem . '.xml';
            $this->put($pub, $relDir, $name, $this->writer->buildUrlSet($chunks[0] ?? []));
            $written[] = $this->relPath($relDir, $name);
            return $written;
        }

        $indexEntries = [];
        foreach ($chunks as $i => $chunk) {
            $childName = $perStem . '-' . ($i + 1) . '.xml';
            $this->put($pub, $relDir, $childName, $this->writer->buildUrlSet($chunk));
            $written[] = $this->relPath($relDir, $childName);
            $indexEntries[] = [
                'loc' => $baseUrl . '/' . ($relDir !== '' ? $relDir . '/' : '') . $childName,
                'lastmod' => date('Y-m-d'),
            ];
        }
        $indexName = $perStem . '.xml';
        $this->put($pub, $relDir, $indexName, $this->writer->buildIndex($indexEntries));
        $written[] = $this->relPath($relDir, $indexName);

        return $written;
    }

    private function put($pub, string $relDir, string $name, string $content): void
    {
        if ($relDir !== '' && !$pub->isExist($relDir)) {
            $pub->create($relDir);
        }
        $pub->writeFile($this->relPath($relDir, $name), $content);
    }

    private function relPath(string $relDir, string $name): string
    {
        return $relDir === '' ? $name : $relDir . '/' . $name;
    }
}
