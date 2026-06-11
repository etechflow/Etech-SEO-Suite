<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::log';

    public function __construct(Context $context, private PageFactory $resultPageFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $page->setActiveMenu('Etechflow_RedirectManager::log');
        $page->getConfig()->getTitle()->prepend(__('404 Log'));
        return $page;
    }
}
