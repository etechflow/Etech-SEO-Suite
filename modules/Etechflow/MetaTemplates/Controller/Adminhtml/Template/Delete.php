<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Etechflow\MetaTemplates\Model\TemplateFactory;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_MetaTemplates::template';

    public function __construct(Context $context, private TemplateFactory $templateFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('template_id');
        if ($id) {
            try {
                $model = $this->templateFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The template has been deleted.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $redirect->setPath('*/*/');
    }
}
