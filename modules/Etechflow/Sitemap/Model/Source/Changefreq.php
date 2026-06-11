<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * sitemaps.org <changefreq> values.
 */
class Changefreq implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $out = [];
        foreach (['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'] as $value) {
            $out[] = ['value' => $value, 'label' => ucfirst($value)];
        }
        return $out;
    }
}
