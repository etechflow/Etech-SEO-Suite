<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Etechflow\RedirectManager\Model\Log;
use Etechflow\RedirectManager\Model\ResourceModel\Log as ResourceLog;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(Log::class, ResourceLog::class);
    }
}
