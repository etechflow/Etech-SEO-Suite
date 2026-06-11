<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Block\Adminhtml\Template\Edit;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    public function __construct(protected Context $context)
    {
    }

    public function getTemplateId()
    {
        return (int)$this->context->getRequest()->getParam('template_id');
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
