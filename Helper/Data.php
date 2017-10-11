<?php

namespace OuterEdge\ZebrecoIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Core\Model\Store;
use OuterEdge\ZebrecoIntegration\Model\Configuration;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    
    /**
     * @var boolean
     */
    protected $isEnabled;

    /**
     * @var string
     */
    protected $account;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @param Store $store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        if ($this->isEnabled === null) {
            $this->isEnabled = $this->scopeConfig->getValue(
                Configuration::XML_PATH_IS_ENABLED,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return $this->isEnabled;
    }

    /**
     * @param Store $store
     * @return string
     */
    protected function getAccount($store = null)
    {
        if ($this->account === null) {
            $this->account = $this->scopeConfig->getValue(
                Configuration::XML_PATH_ACCOUNT_NAME,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return $this->account;
    }

    /**
     * @param Store $store
     * @return string
     */
    protected function getUser($store = null)
    {
        if ($this->user === null) {
            $this->user = $this->scopeConfig->getValue(
                Configuration::XML_PATH_ACCOUNT_USER,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return $this->user;
    }

    /**
     * @param Store $store
     * @return string
     */
    protected function getPassword($store = null)
    {
        if ($this->password === null) {
            $this->password = $this->scopeConfig->getValue(
                Configuration::XML_PATH_ACCOUNT_PASSWORD,
                ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return $this->password;
    }

}