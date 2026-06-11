<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Etechflow\MetaTemplates\Model\TemplateFactory;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_MetaTemplates::template';

    public function __construct(Context $context, private TemplateFactory $templateFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $redirect->setPath('*/*/');
        }

        $id = (int)($data['template_id'] ?? 0);
        $model = $this->templateFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));
                return $redirect->setPath('*/*/');
            }
        }
        if (empty($data['template_id'])) {
            unset($data['template_id']);
        }
        if (($data['category_id'] ?? '') === '') {
            $data['category_id'] = null;
        }

        $model->setData(array_merge($model->getData(), $data));
        try {
            $model->save();
            $this->messageManager->addSuccessMessage(__('The template has been saved.'));
            if ($this->getRequest()->getParam('back')) {
                return $redirect->setPath('*/*/edit', ['template_id' => $model->getId()]);
            }
            return $redirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $redirect->setPath('*/*/edit', ['template_id' => $id]);
        }
    }
}
