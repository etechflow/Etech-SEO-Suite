<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Etechflow\MetaTemplates\Model\TemplateFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_MetaTemplates::template';

    public function __construct(
        Context $context,
        private PageFactory $pageFactory,
        private Registry $registry,
        private TemplateFactory $templateFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('template_id');
        $model = $this->templateFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        }
        $this->registry->register('etechflow_metatemplate', $model);

        $page = $this->pageFactory->create();
        $page->setActiveMenu('Etechflow_MetaTemplates::template');
        $page->getConfig()->getTitle()->prepend($id ? __('Edit Template') : __('New Template'));
        return $page;
    }
}
