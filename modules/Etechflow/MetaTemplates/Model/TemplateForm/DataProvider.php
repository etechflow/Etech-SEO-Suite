<?php
declare(strict_types=1);

namespace Etechflow\MetaTemplates\Model\TemplateForm;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Etechflow\MetaTemplates\Model\ResourceModel\Template\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    private $loadedData;

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

    public function getData()
    {
        if ($this->loadedData !== null) {
            return $this->loadedData;
        }
        $this->loadedData = [];
        foreach ($this->collection->getItems() as $item) {
            $this->loadedData[$item->getId()] = $item->getData();
        }
        return $this->loadedData;
    }
}
