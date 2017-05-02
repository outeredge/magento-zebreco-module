<?php

namespace OuterEdge\ZebrecoIntegration\Model\System\Config\Backend;

/**
 * Backend for serialized array data
 */
class Customermapping extends \Magento\Framework\App\Config\Value
{
    /**
     * Customer attribute mapping
     *
     * @var \OuterEdge\ZebrecoIntegration\Helper\Customermapping
     */
    protected $_zebrecoIntegrationCustomermapping = null;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \OuterEdge\ZebrecoIntegration\Helper\Customermapping $zebrecoIntegrationCustomermapping
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \OuterEdge\ZebrecoIntegration\Helper\Customermapping $zebrecoIntegrationCustomermapping,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_zebrecoIntegrationCustomermapping = $zebrecoIntegrationCustomermapping;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Process data after load
     *
     * @return void
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = $this->_zebrecoIntegrationCustomermapping->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $value = $this->_zebrecoIntegrationCustomermapping->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}
