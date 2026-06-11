<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_MetaTemplates::template';

    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        return $result->forward('edit');
    }
}
