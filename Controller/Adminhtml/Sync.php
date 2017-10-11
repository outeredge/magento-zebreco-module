<?php

namespace OuterEdge\ZebrecoIntegration\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use OuterEdge\ZebrecoIntegration\Helper\Api;
use Magento\Customer\Model\CustomerFactory;

abstract class Sync extends Action
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param Context $context
     * @param Api $api
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Context $context,
        Api $api,
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context);
        $this->api = $api;
        $this->customerFactory = $customerFactory;
    }

    public function getCustomers()
    {
        return $this->customerFactory->create()->getCollection();
    }
}