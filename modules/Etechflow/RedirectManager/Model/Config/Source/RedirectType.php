<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RedirectType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 301, 'label' => __('301 (Permanent)')],
            ['value' => 302, 'label' => __('302 (Temporary)')],
        ];
    }
}
