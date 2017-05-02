<?php

namespace OuterEdge\ZebrecoIntegration\Block\Adminhtml\Form\Field;

class Customermapping extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var Customerattribute
     */
    protected $_attributeRenderer;

    /**
     * Retrieve attribute column renderer
     *
     * @return Customerattribute
     */
    protected function _getAttributeRenderer()
    {
        if (!$this->_attributeRenderer) {
            $this->_attributeRenderer = $this->getLayout()->createBlock(
                'OuterEdge\ZebrecoIntegration\Block\Adminhtml\Form\Field\Customerattribute',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->_attributeRenderer->setClass('customer_attribute_select');
        }
        return $this->_attributeRenderer;
    }

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'customer_attribute_code',
            ['label' => __('Customer Attribute'), 'renderer' => $this->_getAttributeRenderer()]
        );
        $this->addColumn('zebreco_field_name', ['label' => __('Zebreco Field Name')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Mapping');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr['option_' . $this->_getAttributeRenderer()->calcOptionHash($row->getData('customer_attribute_code'))] =
            'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
}
