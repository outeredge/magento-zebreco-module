<?php

namespace OuterEdge\ZebrecoIntegration\Controller\Adminhtml\Sync;

use ZebrecoPHP\Api as ZebrecoApi;

class All extends \OuterEdge\ZebrecoIntegration\Controller\Adminhtml\Sync
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('customer/index');

        if (!$this->_zebrecoIntegrationHelper->isEnabled()) {
            $this->messageManager->addWarning(__('The Zebreco API is not enabled.'));
            return $resultRedirect;
        }

        try {
            $customers = $this->getCustomers();
            if (!empty($customers)) {

                $zebrecoData = array();
                foreach ($customers as $customer) {
                    $customerData = array(
                        'email' => $customer->getEmail(),
                        'name'  => $customer->getFirstname() . ' ' . $customer->getLastname(),
                    );
                    $addresses = $customer->getPrimaryAddresses();
                    if (!empty($addresses)) {
                        $customerData = array_merge($customerData, array(
                            'street'    => implode(', ', $addresses[0]->getStreet()),
                            'city'      => $addresses[0]->getCity(),
                            'county'    => $addresses[0]->getRegion(),
                            'postcode'  => $addresses[0]->getPostcode(),
                            'telephone' => $addresses[0]->getTelephone()
                        ));
                    }
                    $zebrecoData[] = $customerData;
                }

                $zebrecoApi = new ZebrecoApi(
                    $this->_zebrecoIntegrationHelper->getAccount(),
                    $this->_zebrecoIntegrationHelper->getUser(),
                    $this->_zebrecoIntegrationHelper->getPassword(),
                    'contact'
                );
                $zebrecoApi->update($zebrecoData);

                $this->messageManager->addSuccess(__('Customers have been successfully synced to Zebreco.'));
            }
            return $resultRedirect;

        } catch (Exception $e) {
            $this->messageManager->addException($e);
            return $resultRedirect;
        }
    }
}