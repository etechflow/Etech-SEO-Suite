<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Etechflow\RedirectManager\Model\LogFactory;
use Etechflow\RedirectManager\Model\ResourceModel\Log as ResourceLog;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::log';

    public function __construct(
        Context $context,
        private LogFactory $logFactory,
        private ResourceLog $resource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('log_id');
        if ($id) {
            try {
                $model = $this->logFactory->create();
                $this->resource->load($model, $id);
                $this->resource->delete($model);
                $this->messageManager->addSuccessMessage(__('Log entry deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
