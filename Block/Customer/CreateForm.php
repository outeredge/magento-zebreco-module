<?php

namespace OuterEdge\ZebrecoIntegration\Block\Customer;

use Magento\Framework\View\Element\Template;

class CreateForm extends Template
{
    /**
     * Returns action url for support ticket create form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('support/customer/createPost', ['_secure' => true]);
    }
}
