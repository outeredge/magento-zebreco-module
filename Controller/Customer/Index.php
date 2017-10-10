<?php

namespace OuterEdge\ZebrecoIntegration\Controller\Customer;

use OuterEdge\ZebrecoIntegration\Controller\Customer as CustomerController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;

class Index extends CustomerController
{
    /**
     * Render my support tickets
     *
     * @return Page
     */
    public function execute()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('support/customer');
        }
        /*if ($block = $resultPage->getLayout()->getBlock('review_customer_list')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }*/
        $resultPage->getConfig()->getTitle()->set(__('My Support Tickets'));
        return $resultPage;
    }
}
