<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Redirect extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('etechflow_redirect', 'entity_id');
    }

    public function incrementHits(int $id): void
    {
        $c = $this->getConnection();
        $c->update($this->getMainTable(), ['hits' => new \Zend_Db_Expr('hits + 1')], ['entity_id = ?' => $id]);
    }
}
