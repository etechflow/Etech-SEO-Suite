<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model\Provider;

use Etechflow\Sitemap\Model\SitemapItem;

/**
 * A source of sitemap <url> entries for a given store view.
 */
interface ProviderInterface
{
    /** Short machine type, e.g. "product". */
    public function getType(): string;

    public function isEnabled(int $storeId): bool;

    /**
     * @return SitemapItem[] list (not keyed); each carries a stable cross-store key.
     */
    public function getItems(int $storeId): array;
}
