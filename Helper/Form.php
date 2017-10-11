<?php

namespace OuterEdge\ZebrecoIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Form extends AbstractHelper
{
    /**
     * @var array
     */
    private $postData = null;
    
    /**
     * Get value from POST by key
     *
     * @param string $key
     * @return string
     */
    public function getPostValue($key)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->getDataPersistor()->get('support_ticket');
            $this->getDataPersistor()->clear('support_ticket');
        }

        if (isset($this->postData[$key])) {
            return (string) $this->postData[$key];
        }

        return '';
    }

    /**
     * Get Data Persistor
     *
     * @return DataPersistorInterface
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
}