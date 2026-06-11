<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    public function __construct(private ScopeConfigInterface $scopeConfig)
    {
    }

    public function isEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag('etechflow_metatemplates/general/enabled', ScopeInterface::SCOPE_STORE, $storeId);
    }

    /** override = always apply; otherwise only fill empty entity meta. */
    public function isOverride($storeId = null): bool
    {
        return (string)$this->scopeConfig->getValue('etechflow_metatemplates/general/mode', ScopeInterface::SCOPE_STORE, $storeId) === 'override';
    }
}
