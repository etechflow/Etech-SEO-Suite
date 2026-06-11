<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model;

/**
 * Serialises SitemapItem lists into sitemaps.org-compliant XML
 * (urlset documents) and a sitemapindex document.
 */
class Writer
{
    private const NS_SITEMAP = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    private const NS_IMAGE = 'http://www.google.com/schemas/sitemap-image/1.1';
    private const NS_XHTML = 'http://www.w3.org/1999/xhtml';

    /**
     * @param SitemapItem[] $items
     */
    public function buildUrlSet(array $items): string
    {
        $hasImages = false;
        $hasAlternates = false;
        foreach ($items as $item) {
            if ($item->images) {
                $hasImages = true;
            }
            if ($item->alternates) {
                $hasAlternates = true;
            }
            if ($hasImages && $hasAlternates) {
                break;
            }
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="' . self::NS_SITEMAP . '"';
        if ($hasImages) {
            $xml .= ' xmlns:image="' . self::NS_IMAGE . '"';
        }
        if ($hasAlternates) {
            $xml .= ' xmlns:xhtml="' . self::NS_XHTML . '"';
        }
        $xml .= ">\n";

        foreach ($items as $item) {
            $xml .= $this->renderUrl($item);
        }

        $xml .= '</urlset>' . "\n";
        return $xml;
    }

    /**
     * @param array<int,array{loc:string,lastmod:?string}> $entries
     */
    public function buildIndex(array $entries): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="' . self::NS_SITEMAP . '">' . "\n";
        foreach ($entries as $entry) {
            $xml .= '    <sitemap>' . "\n";
            $xml .= '        <loc>' . $this->escape($entry['loc']) . '</loc>' . "\n";
            if (!empty($entry['lastmod'])) {
                $xml .= '        <lastmod>' . $this->escape($this->formatDate($entry['lastmod'])) . '</lastmod>' . "\n";
            }
            $xml .= '    </sitemap>' . "\n";
        }
        $xml .= '</sitemapindex>' . "\n";
        return $xml;
    }

    private function renderUrl(SitemapItem $item): string
    {
        $xml = '    <url>' . "\n";
        $xml .= '        <loc>' . $this->escape($item->loc) . '</loc>' . "\n";
        if ($item->lastmod) {
            $xml .= '        <lastmod>' . $this->escape($this->formatDate($item->lastmod)) . '</lastmod>' . "\n";
        }
        if ($item->changefreq) {
            $xml .= '        <changefreq>' . $this->escape($item->changefreq) . '</changefreq>' . "\n";
        }
        if ($item->priority !== '') {
            $xml .= '        <priority>' . $this->escape($item->priority) . '</priority>' . "\n";
        }
        foreach ($item->alternates as $hreflang => $href) {
            $xml .= '        <xhtml:link rel="alternate" hreflang="' . $this->escape($hreflang)
                . '" href="' . $this->escape($href) . '"/>' . "\n";
        }
        foreach ($item->images as $image) {
            $xml .= '        <image:image>' . "\n";
            $xml .= '            <image:loc>' . $this->escape($image['loc']) . '</image:loc>' . "\n";
            if (!empty($image['title'])) {
                $xml .= '            <image:title>' . $this->escape($image['title']) . '</image:title>' . "\n";
            }
            $xml .= '        </image:image>' . "\n";
        }
        $xml .= '    </url>' . "\n";
        return $xml;
    }

    private function formatDate(string $value): string
    {
        // DB datetimes are 'Y-m-d H:i:s'; the date-only form is valid and stable.
        $ts = strtotime($value);
        return $ts ? date('Y-m-d', $ts) : substr($value, 0, 10);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
