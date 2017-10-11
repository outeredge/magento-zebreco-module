<?php

namespace OuterEdge\ZebrecoIntegration\Block\Customer;

use Magento\Framework\View\Element\Template;

class CreateForm extends Template
{
    /**
     * Returns action url for support ticket form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('support/customer/post', ['_secure' => true]);
    }
}
