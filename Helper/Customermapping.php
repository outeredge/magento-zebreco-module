<?php

namespace OuterEdge\ZebrecoIntegration\Helper;

use Magento\Store\Model\Store;

/**
 * Customermapping value manipulation helper
 */
class Customermapping
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Math\Random $mathRandom
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Math\Random $mathRandom
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->mathRandom = $mathRandom;
    }

    /**
     * Generate a storable representation of a value
     *
     * @param int|float|string|array $value
     * @return string
     */
    protected function serializeValue($value)
    {
        if (is_numeric($value)) {
            $data = (float) $value;
            return (string) $data;
        } elseif (is_array($value)) {
            $data = [];
            foreach ($value as $attributeCode => $zebrecoFieldName) {
                if (!array_key_exists($attributeCode, $data)) {
                    $data[$attributeCode] = $zebrecoFieldName;
                }
            }
            return serialize($data);
        } else {
            return '';
        }
    }

    /**
     * Create a value from a storable representation
     *
     * @param int|float|string $value
     * @return array
     */
    protected function unserializeValue($value)
    {
        if (is_numeric($value)) {
            return [];
        } elseif (is_string($value) && !empty($value)) {
            return unserialize($value);
        } else {
            return [];
        }
    }

    /**
     * Check whether value is in form retrieved by _encodeArrayFieldValue()
     *
     * @param string|array $value
     * @return bool
     */
    protected function isEncodedArrayFieldValue($value)
    {
        if (!is_array($value)) {
            return false;
        }
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('customer_attribute_code', $row)
                || !array_key_exists('zebreco_field_name', $row)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $attributeCode => $zebrecoFieldName) {
            $resultId = $this->mathRandom->getUniqueHash('_');
            $result[$resultId] = ['customer_attribute_code' => $attributeCode, 'zebreco_field_name' => $zebrecoFieldName];
        }
        return $result;
    }

    /**
     * Decode value from used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function decodeArrayFieldValue(array $value)
    {
        $result = [];
        unset($value['__empty']);
        foreach ($value as $row) {
            if (!is_array($row)
                || !array_key_exists('customer_attribute_code', $row)
                || !array_key_exists('zebreco_field_name', $row)
            ) {
                continue;
            }
            $attributeCode = $row['customer_attribute_code'];
            $zebrecoFieldName = $row['zebreco_field_name'];
            $result[$attributeCode] = $zebrecoFieldName;
        }
        return $result;
    }

    /**
     * Retrieve zebreco_field_name value from config
     *
     * @param string $customerAttributeCode
     * @param null|string|bool|int|Store $store
     * @return float|null
     */
    public function getConfigValue($customerAttributeCode, $store = null)
    {
        $value = $this->scopeConfig->getValue(
            \OuterEdge\ZebrecoIntegration\Model\Configuration::XML_PATH_CUSTOMER_MAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $value = $this->unserializeValue($value);
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $result = null;
        foreach ($value as $attributeCode => $zebrecoFieldName) {
            if ($attributeCode == $customerAttributeCode) {
                $result = $zebrecoFieldName;
                break;
            }
        }
        return $result;
    }

    /**
     * Make value readable by \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param string|array $value
     * @return array
     */
    public function makeArrayFieldValue($value)
    {
        $value = $this->unserializeValue($value);
        if (!$this->isEncodedArrayFieldValue($value)) {
            $value = $this->encodeArrayFieldValue($value);
        }
        return $value;
    }

    /**
     * Make value ready for store
     *
     * @param string|array $value
     * @return string
     */
    public function makeStorableArrayFieldValue($value)
    {
        if ($this->isEncodedArrayFieldValue($value)) {
            $value = $this->decodeArrayFieldValue($value);
        }
        $value = $this->serializeValue($value);
        return $value;
    }
}
