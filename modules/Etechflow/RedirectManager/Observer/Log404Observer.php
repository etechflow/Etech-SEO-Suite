<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;
use Etechflow\RedirectManager\Model\ResourceModel\Log as LogResource;

/**
 * Logs 404 (no-route) hits so they can be turned into redirects from the admin.
 */
class Log404Observer implements ObserverInterface
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig,
        private StoreManagerInterface $storeManager,
        private Http $request,
        private LogResource $logResource
    ) {
    }

    public function execute(Observer $observer): void
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_redirectmanager/general/enabled', ScopeInterface::SCOPE_STORE)
            || !$this->scopeConfig->isSetFlag('etechflow_redirectmanager/log404/enabled', ScopeInterface::SCOPE_STORE)
        ) {
            return;
        }
        try {
            $path = trim((string)$this->request->getPathInfo(), '/');
            if ($path === '') {
                return;
            }
            $exclude = (string)$this->scopeConfig->getValue(
                'etechflow_redirectmanager/log404/exclude_patterns',
                ScopeInterface::SCOPE_STORE
            );
            if ($exclude !== '' && @preg_match('#' . $exclude . '#i', $path)) {
                return;
            }
            $storeId  = (int)$this->storeManager->getStore()->getId();
            $referrer = (string)$this->request->getServerValue('HTTP_REFERER');
            $this->logResource->logHit($path, $referrer !== '' ? $referrer : null, $storeId);
        } catch (\Throwable $e) {
            // never break the 404 page
        }
    }
}
