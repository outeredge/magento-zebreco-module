<?php

namespace OuterEdge\ZebrecoIntegration\Controller\Adminhtml;

abstract class Sync extends \Magento\Backend\App\Action
{
    /**
     * @var \OuterEdge\ZebrecoIntegration\Helper\Data
     */
    protected $_zebrecoIntegrationHelper;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \OuterEdge\ZebrecoIntegration\Helper\Data $zebrecoIntegrationHelper
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \OuterEdge\ZebrecoIntegration\Helper\Data $zebrecoIntegrationHelper,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->_zebrecoIntegrationHelper = $zebrecoIntegrationHelper;
        $this->_customerFactory = $customerFactory;
    }

    public function getCustomers()
    {
        return $this->_customerFactory->create()->getCollection();
    }
}