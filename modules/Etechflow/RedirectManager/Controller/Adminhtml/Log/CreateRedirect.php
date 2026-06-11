<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Etechflow\RedirectManager\Model\LogFactory;
use Etechflow\RedirectManager\Model\ResourceModel\Log as ResourceLog;
use Etechflow\RedirectManager\Model\RedirectFactory;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect as ResourceRedirect;

/**
 * Creates a draft redirect from a 404-log row and opens it for editing.
 */
class CreateRedirect extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::redirect';

    public function __construct(
        Context $context,
        private LogFactory $logFactory,
        private ResourceLog $logResource,
        private RedirectFactory $redirectFactory,
        private ResourceRedirect $redirectResource,
        private ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $logId = (int)$this->getRequest()->getParam('log_id');

        $log = $this->logFactory->create();
        $this->logResource->load($log, $logId);
        if (!$log->getId()) {
            $this->messageManager->addErrorMessage(__('Log entry not found.'));
            return $resultRedirect->setPath('*/log/');
        }

        $type = (int)$this->scopeConfig->getValue('etechflow_redirectmanager/redirects/default_type') ?: 301;
        $redirect = $this->redirectFactory->create()->setData([
            'request_path'  => (string)$log->getData('request_path'),
            'target_path'   => '/',
            'redirect_type' => $type,
            'store_id'      => (int)$log->getData('store_id'),
            'is_active'     => 1,
        ]);

        try {
            $this->redirectResource->save($redirect);
            $log->setData('redirect_id', $redirect->getId());
            $this->logResource->save($log);
            $this->messageManager->addSuccessMessage(__('Draft redirect created — set the target URL and save.'));
            return $resultRedirect->setPath('*/redirect/edit', ['entity_id' => $redirect->getId()]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/log/');
        }
    }
}
