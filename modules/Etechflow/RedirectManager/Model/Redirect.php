<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model;

use Magento\Framework\Model\AbstractModel;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect as ResourceRedirect;

class Redirect extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(ResourceRedirect::class);
    }
}
