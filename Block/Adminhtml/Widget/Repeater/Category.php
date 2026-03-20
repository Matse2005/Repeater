<?php

declare(strict_types=1);

namespace MatseVH\Repeater\Block\Adminhtml\Widget\Repeater;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Store\Model\Store;
use MatseVH\Repeater\Block\Adminhtml\Widget\Repeater\Fields\Hr;
use MatseVH\Repeater\Block\Adminhtml\Widget\Repeater\Fields\Text;
use MatseVH\Repeater\Block\Adminhtml\Widget\Repeater\Fields\Textarea;

use function Psl\Str\capitalize;

class Category
{
    protected string $label = "category";
    protected array $fields = [];

    public function __construct(
        private readonly CategoryCollectionFactory $categoryCollectionFactory,
    ) {
        $this->fields = [
            new Text('category_id', 'Category', required: true),
            new Hr(),
            new Text('category_id_copy', 'Category', required: true),
            new Textarea('test', 'test'),
        ];
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getLabel(): string
    {
        return capitalize($this->label);
    }

    private function buildCategoryOptions(): array
    {
        $options = [];

        $collection = $this->categoryCollectionFactory->create();
        $collection->setStore(Store::DEFAULT_STORE_ID)
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('level', ['gt' => 1])
            ->setOrder('path', 'ASC');

        foreach ($collection as $category) {
            $depth   = max(0, $category->getLevel() - 2);
            $padding = str_repeat('— ', $depth);
            $options[(string) $category->getId()] = $padding . $category->getName();
        }

        return $options;
    }
}
