<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Redirect as RedirectAction;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect\CollectionFactory;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect as RedirectResource;

/**
 * Matches the incoming path against active redirects and issues a 301/302.
 * Registered before urlrewrite so managed redirects take precedence.
 */
class Router implements RouterInterface
{
    public function __construct(
        private ActionFactory $actionFactory,
        private ResponseInterface $response,
        private ScopeConfigInterface $scopeConfig,
        private StoreManagerInterface $storeManager,
        private UrlInterface $url,
        private CollectionFactory $collectionFactory,
        private RedirectResource $redirectResource
    ) {
    }

    public function match(RequestInterface $request)
    {
        if (!$this->scopeConfig->isSetFlag('etechflow_redirectmanager/general/enabled', ScopeInterface::SCOPE_STORE)) {
            return null;
        }
        $path = trim((string)$request->getPathInfo(), '/');
        if ($path === '') {
            return null;
        }
        try {
            $storeId = (int)$this->storeManager->getStore()->getId();
        } catch (\Throwable $e) {
            return null;
        }

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('request_path', $path)
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('store_id', ['in' => [0, $storeId]])
            ->setOrder('store_id', 'DESC')
            ->setPageSize(1);
        $redirect = $collection->getFirstItem();
        if (!$redirect->getId()) {
            return null;
        }

        $target = (string)$redirect->getData('target_path');
        if (!preg_match('#^https?://#i', $target)) {
            $target = $this->url->getDirectUrl(ltrim($target, '/'));
        }
        try {
            $this->redirectResource->incrementHits((int)$redirect->getId());
        } catch (\Throwable $e) {
            // best-effort hit counter; never block the redirect
        }

        $this->response->setRedirect($target, (int)$redirect->getData('redirect_type'));
        $request->setDispatched(true);
        return $this->actionFactory->create(RedirectAction::class);
    }
}
