<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model;

use Magento\Framework\Model\AbstractModel;
use Etechflow\RedirectManager\Model\ResourceModel\Log as ResourceLog;

class Log extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(ResourceLog::class);
    }
}
