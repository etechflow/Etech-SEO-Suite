<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Redirect;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Etechflow\RedirectManager\Model\RedirectFactory;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect as ResourceRedirect;

class Save extends Action
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
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        $model = $this->redirectFactory->create();
        $id = (int)($data['entity_id'] ?? 0);
        if ($id) {
            $this->resource->load($model, $id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This redirect no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        $data['request_path'] = ltrim(trim((string)($data['request_path'] ?? '')), '/');
        $data['target_path']  = trim((string)($data['target_path'] ?? ''));
        if ($data['request_path'] === '' || $data['target_path'] === '') {
            $this->messageManager->addErrorMessage(__('Request path and target are required.'));
            return $resultRedirect->setPath('*/*/edit', $id ? ['entity_id' => $id] : []);
        }
        if (!$id) {
            unset($data['entity_id']);
        }

        $model->setData(array_merge($model->getData(), $data));
        try {
            $this->resource->save($model);
            $this->messageManager->addSuccessMessage(__('Redirect saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/edit', $id ? ['entity_id' => $id] : []);
        }
    }
}
