<?php

namespace OuterEdge\ZebrecoIntegration\Block\Customer;

use Magento\Framework\View\Element\Template;

class ReplyForm extends Template
{
    /**
     * Returns action url for support ticket reply form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('support/customer/replyPost', ['_secure' => true, '_current' => true]);
    }
}
