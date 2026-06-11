<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class Stores implements OptionSourceInterface
{
    public function __construct(private StoreManagerInterface $storeManager)
    {
    }

    public function toOptionArray(): array
    {
        $out = [['value' => 0, 'label' => __('All Store Views')]];
        foreach ($this->storeManager->getStores() as $store) {
            $out[] = ['value' => (int)$store->getId(), 'label' => $store->getName()];
        }
        return $out;
    }
}
