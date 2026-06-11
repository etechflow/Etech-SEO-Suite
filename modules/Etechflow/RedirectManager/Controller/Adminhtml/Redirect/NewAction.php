<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Redirect;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\ForwardFactory;

class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::redirect';

    public function __construct(Context $context, private ForwardFactory $resultForwardFactory)
    {
        parent::__construct($context);
    }

    public function execute()
    {
        return $this->resultForwardFactory->create()->forward('edit');
    }
}
