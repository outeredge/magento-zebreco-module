<?php

namespace OuterEdge\ZebrecoIntegration\Block\Adminhtml\Form\Field;

/**
 * HTML select element block with customer attributes options
 */
class Customerattribute extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Customer attributes cache
     *
     * @var array
     */
    private $_customerAttributes;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_customerFactory = $customerFactory;
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
            foreach ($this->_customerFactory->create()->getAttributes() as $attributeModel) {
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
