<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\UrlInterface;

class LogActions extends Column
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

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }
        $name = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            if (empty($item['log_id'])) {
                continue;
            }
            $id = $item['log_id'];
            $item[$name]['create'] = [
                'href'  => $this->urlBuilder->getUrl('redirectmanager/log/createRedirect', ['log_id' => $id]),
                'label' => __('Create Redirect'),
            ];
            $item[$name]['delete'] = [
                'href'    => $this->urlBuilder->getUrl('redirectmanager/log/delete', ['log_id' => $id]),
                'label'   => __('Delete'),
                'confirm' => ['title' => __('Delete'), 'message' => __('Delete this log entry?')],
            ];
        }
        return $dataSource;
    }
}
