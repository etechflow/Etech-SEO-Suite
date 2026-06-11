<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('etechflow_redirect_404_log', 'log_id');
    }

    /** Upsert a 404 hit (increment count on repeat). */
    public function logHit(string $path, ?string $referrer, int $storeId): void
    {
        $c = $this->getConnection();
        $table = $this->getMainTable();
        $c->query(
            "INSERT INTO {$table} (request_path, referrer, store_id, hits) VALUES (?, ?, ?, 1) "
            . "ON DUPLICATE KEY UPDATE hits = hits + 1, referrer = VALUES(referrer), updated_at = CURRENT_TIMESTAMP",
            [$path, $referrer, $storeId]
        );
    }
}
