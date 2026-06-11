<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model\ResourceModel\Redirect;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Etechflow\RedirectManager\Model\Redirect;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect as ResourceRedirect;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(Redirect::class, ResourceRedirect::class);
    }
}
