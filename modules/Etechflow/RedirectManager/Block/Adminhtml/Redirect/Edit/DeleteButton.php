<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Block\Adminhtml\Redirect\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $id = $this->getEntityId();
        if (!$id) {
            return [];
        }
        return [
            'label'      => __('Delete'),
            'class'      => 'delete',
            'on_click'   => 'deleteConfirm(\'' . __('Delete this redirect?') . '\', \''
                . $this->getUrl('*/*/delete', ['entity_id' => $id]) . '\')',
            'sort_order' => 20,
        ];
    }
}
