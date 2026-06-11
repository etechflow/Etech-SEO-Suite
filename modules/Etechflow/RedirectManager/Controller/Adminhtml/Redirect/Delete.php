<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Redirect;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Etechflow\RedirectManager\Model\RedirectFactory;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect as ResourceRedirect;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::redirect';

    public function __construct(
        Context $context,
        private RedirectFactory $redirectFactory,
        private ResourceRedirect $resource
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id');
        if ($id) {
            try {
                $model = $this->redirectFactory->create();
                $this->resource->load($model, $id);
                $this->resource->delete($model);
                $this->messageManager->addSuccessMessage(__('Redirect deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
