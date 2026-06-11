<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class AppliesTo implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'product', 'label' => __('Product pages')],
            ['value' => 'category', 'label' => __('Category pages')],
            ['value' => 'cms_page', 'label' => __('CMS pages')],
        ];
    }
}
