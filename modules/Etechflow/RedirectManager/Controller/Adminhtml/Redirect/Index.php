<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Redirect;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::redirect';

    public function __construct(Context $context, private PageFactory $resultPageFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_RedirectManager::redirect');
        $page->getConfig()->getTitle()->prepend(__('Redirects'));
        return $page;
    }
}
