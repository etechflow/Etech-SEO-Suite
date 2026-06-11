<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_MetaTemplates::template';

    public function __construct(Context $context, private PageFactory $pageFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Etechflow_MetaTemplates::template');
        $page->getConfig()->getTitle()->prepend(__('Meta Templates'));
        return $page;
    }
}
