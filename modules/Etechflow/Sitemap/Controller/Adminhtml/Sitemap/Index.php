<?php
declare(strict_types=1);

namespace Etechflow\Sitemap\Controller\Adminhtml\Sitemap;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Etechflow_Sitemap::sitemap';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Etechflow_Sitemap::sitemap');
        $resultPage->getConfig()->getTitle()->prepend(__('Etechflow Sitemap'));
        return $resultPage;
    }
}
