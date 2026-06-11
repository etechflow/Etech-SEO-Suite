<?php
declare(strict_types=1);

namespace Etechflow\RedirectManager\Model\RedirectForm;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Etechflow\RedirectManager\Model\ResourceModel\Redirect\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        $result = [];
        foreach ($this->collection->getItems() as $item) {
            $result[$item->getId()] = $item->getData();
        }
        return $result;
    }
}
