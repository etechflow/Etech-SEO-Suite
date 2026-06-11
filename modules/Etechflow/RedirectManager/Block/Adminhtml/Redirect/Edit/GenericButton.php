<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Block\Adminhtml\Redirect\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    public function __construct(protected Context $context)
    {
    }

    public function getEntityId(): int
    {
        return (int)$this->context->getRequest()->getParam('entity_id');
    }

    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
