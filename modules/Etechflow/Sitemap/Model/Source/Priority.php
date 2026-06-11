<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * sitemaps.org <priority> values 0.0 – 1.0.
 */
class Priority implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $out = [];
        foreach (['1.0', '0.9', '0.8', '0.75', '0.5', '0.25', '0.1', '0.0'] as $value) {
            $out[] = ['value' => $value, 'label' => $value];
        }
        return $out;
    }
}
