<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Model;

use Magento\Framework\Model\AbstractModel;
use Etechflow\MetaTemplates\Model\ResourceModel\Template as ResourceTemplate;

class Template extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(ResourceTemplate::class);
    }
}
