<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Block\Adminhtml\Template\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        if (!$this->getTemplateId()) {
            return [];
        }
        return [
            'label'      => __('Delete'),
            'class'      => 'delete',
            'on_click'   => sprintf(
                "deleteConfirm('%s', '%s')",
                __('Are you sure you want to delete this template?'),
                $this->getUrl('*/*/delete', ['template_id' => $this->getTemplateId()])
            ),
            'sort_order' => 20,
        ];
    }
}
