<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model;

/**
 * One <url> entry. Immutable value object passed from providers to the writer.
 */
class SitemapItem
{
    /**
     * @param string $key  Stable cross-store identity ("product:123"), used to group hreflang alternates.
     * @param string $loc  Absolute URL.
     * @param array<int,array{loc:string,title:?string}> $images
     * @param array<string,string> $alternates  hreflang code => absolute URL
     */
    public function __construct(
        public readonly string $key,
        public readonly string $loc,
        public readonly ?string $lastmod,
        public readonly string $changefreq,
        public readonly string $priority,
        public readonly array $images = [],
        public array $alternates = []
    ) {
    }
}
