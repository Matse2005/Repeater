<?php

declare(strict_types=1);

namespace MatseVH\Repeater\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context as TemplateContext;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Widget\Block\BlockInterface;
use MatseVH\Repeater\Block\Adminhtml\Widget\Repeater\AbstractField;
use Psl\Json;
use Psl\Type;

class Repeater extends Template implements BlockInterface
{
    protected $_template = 'MatseVH_Repeater::widget/repeater.phtml';

    protected ElementFactory $_elementFactory;
    protected AbstractElement $_element;

    public function __construct(
        TemplateContext $context,
        ElementFactory $elementFactory,
        private readonly ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_elementFactory = $elementFactory;
        AbstractField::setEscaper($context->getEscaper());

        parent::__construct($context, $data);
    }

    public function prepareElementHtml(AbstractElement $element): AbstractElement
    {
        $this->_element = $element;

        $existingValue = $element->getValue();
        $html = $this->toHtml();

        $element->setData('after_element_html', $html);
        $element->setValue($existingValue ?: '[]');

        if ($element->getRequired()) {
            $element->addClass('required-entry');
        }

        return $element;
    }

    public function getElement(): AbstractElement
    {
        return $this->_element;
    }

    public function getRepeaterData(): object
    {
        return $this->objectManager->get(trim($this->getData('repeater')));
    }

    /** @return AbstractField[] */
    public function getFields(): array
    {
        return $this->getRepeaterData()->getFields();
    }

    public function getLabel(): string
    {
        return $this->getRepeaterData()->getLabel();
    }

    public function getFieldsJson(): string
    {
        return Json\encode($this->getFields());
    }

    public function getExistingRowsJson(): string
    {
        $value = $this->_element->getValue();

        if (!$value || '[]' === $value) {
            return '[]';
        }

        try {
            $decoded = Json\typed($value, Type\vec(Type\dict(Type\string(), Type\mixed())));
        } catch (Json\Exception\DecodeException) {
            return '[]';
        }

        return Json\encode($decoded);
    }

    public function getFieldTemplatesJson(): string
    {
        $templates = [];

        foreach ($this->getFields() as $field) {
            if (!$field instanceof AbstractField) {
                throw new \RuntimeException(
                    'Field must be an instance of AbstractField, got: ' . get_debug_type($field)
                );
            }

            $result = $field->render($this->_element->getId());

            $html = match ($result['type']) {
                'html' => $result['value'],
                'template' => $this->getLayout()
                    ->createBlock(Template::class)
                    ->setTemplate($result['value'])
                    ->setField($field)
                    ->setElementId($this->_element->getId())
                    ->toHtml(),
            };

            $templates[] = [
                'data' => $field->getData(),
                'label' => $field->showLabel() ? $field->renderLabelHtml($this->_element->getId()) : '',
                'html' => $html,
            ];
        }

        return Json\encode($templates);
    }
}
