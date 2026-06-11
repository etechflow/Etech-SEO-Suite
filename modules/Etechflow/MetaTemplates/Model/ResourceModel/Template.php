<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Template extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('etechflow_metatemplate', 'template_id');
    }
}
