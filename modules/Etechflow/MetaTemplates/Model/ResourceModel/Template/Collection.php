<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Model\ResourceModel\Template;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Etechflow\MetaTemplates\Model\Template;
use Etechflow\MetaTemplates\Model\ResourceModel\Template as ResourceTemplate;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(Template::class, ResourceTemplate::class);
    }
}
