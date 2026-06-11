<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Etechflow\RedirectManager\Model\ResourceModel\Log\CollectionFactory;

class MassDelete extends Action
{
    const ADMIN_RESOURCE = 'Etechflow_RedirectManager::log';

    public function __construct(
        Context $context,
        private Filter $filter,
        private CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $count = 0;
        foreach ($collection as $item) {
            $item->delete();
            $count++;
        }
        $this->messageManager->addSuccessMessage(__('%1 log entry(ies) deleted.', $count));
        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
