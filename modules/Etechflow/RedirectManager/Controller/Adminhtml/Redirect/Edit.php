<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Redirect;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::redirect';

    public function __construct(Context $context, private PageFactory $resultPageFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('entity_id');
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_RedirectManager::redirect');
        $page->getConfig()->getTitle()->prepend($id ? __('Edit Redirect') : __('New Redirect'));
        return $page;
    }
}
