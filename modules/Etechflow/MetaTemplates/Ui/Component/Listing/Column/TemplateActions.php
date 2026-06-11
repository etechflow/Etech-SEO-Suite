<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class TemplateActions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }
        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['template_id'])) {
                continue;
            }
            $id = $item['template_id'];
            $item[$this->getData('name')] = [
                'edit' => [
                    'href'  => $this->urlBuilder->getUrl('metatemplates/template/edit', ['template_id' => $id]),
                    'label' => __('Edit'),
                ],
                'delete' => [
                    'href'    => $this->urlBuilder->getUrl('metatemplates/template/delete', ['template_id' => $id]),
                    'label'   => __('Delete'),
                    'confirm' => [
                        'title'   => __('Delete template'),
                        'message' => __('Are you sure you want to delete this template?'),
                    ],
                ],
            ];
        }
        return $dataSource;
    }
}
