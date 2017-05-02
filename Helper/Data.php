<?php

namespace OuterEdge\ZebrecoIntegration\Helper;

class Data
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var string
     */
    protected $_account;

    /**
     * @var string
     */
    protected $_user;

    /**
     * @var string
     */
    protected $_password;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Core\Model\Store $store
     * @return boolean
     */
    public function isEnabled($store = null)
    {
        return !empty($this->getAccount($store));
    }

    /**
     * @param \Magento\Core\Model\Store $store
     * @return string
     */
    public function getAccount($store = null)
    {
        if ($this->_account === null) {
            $this->_account = $this->_scopeConfig->getValue(
                \OuterEdge\ZebrecoIntegration\Model\Configuration::XML_PATH_ACCOUNT_NAME,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return $this->_account;
    }

    /**
     * @param \Magento\Core\Model\Store $store
     * @return string
     */
    public function getUser($store = null)
    {
        if ($this->_user === null) {
            $this->_user = $this->_scopeConfig->getValue(
                \OuterEdge\ZebrecoIntegration\Model\Configuration::XML_PATH_ACCOUNT_USER,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return $this->_user;
    }

    /**
     * @param \Magento\Core\Model\Store $store
     * @return string
     */
    public function getPassword($store = null)
    {
        if ($this->_password === null) {
            $this->_password = $this->_scopeConfig->getValue(
                \OuterEdge\ZebrecoIntegration\Model\Configuration::XML_PATH_ACCOUNT_PASSWORD,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );
        }
        return $this->_password;
    }

}