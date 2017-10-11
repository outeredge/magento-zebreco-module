<?php

namespace OuterEdge\ZebrecoIntegration\Controller\Customer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use OuterEdge\ZebrecoIntegration\Controller\Customer as CustomerController;

class Create extends CustomerController
{
    /**
     * Render create support ticket page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /*$review = $this->reviewFactory->create()->load($this->getRequest()->getParam('id'));
        if ($review->getCustomerId() != $this->customerSession->getCustomerId()) {
            * @var \Magento\Framework\Controller\Result\Forward $resultForward 
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }*/
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('support/customer');
        }
        $resultPage->getConfig()->getTitle()->set(__('Create Support Ticket'));
        return $resultPage;
    }
}
