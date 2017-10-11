<?php

namespace OuterEdge\ZebrecoIntegration\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Magento\Customer\Model\CustomerFactory;

class Customerattribute extends Select
{
    /**
     * @var array
     */
    protected $customerAttributes;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param Context $context
     * @param CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve allowed customer attributes
     *
     * @param string $attributeCode return name by customer attribute id
     * @return array|string
     */
    protected function _getCustomerAttributes($attributeCode = null)
    {
        if ($this->_customerAttributes === null) {
            $this->_customerAttributes = [];
            foreach ($this->customerFactory->create()->getAttributes() as $attributeModel) {
                $this->_customerAttributes[$attributeModel->getAttributeCode()] = $attributeModel->getAttributeCode();
            }
        }
        if ($attributeCode !== null) {
            return isset($this->_customerAttributes[$attributeCode]) ? $this->_customerAttributes[$attributeCode] : null;
        }
        return $this->_customerAttributes;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getCustomerAttributes() as $attributeCode) {
                $this->addOption($attributeCode, addslashes($attributeCode));
            }
        }
        return parent::_toHtml();
    }
}
