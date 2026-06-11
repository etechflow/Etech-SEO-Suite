<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Mode implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'fill_empty', 'label' => __('Only fill empty meta (keep manual values)')],
            ['value' => 'override', 'label' => __('Always override')],
        ];
    }
}
