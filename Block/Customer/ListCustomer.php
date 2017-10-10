<?php

namespace OuterEdge\ZebrecoIntegration\Block\Customer;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Block\Account\Dashboard;
use Magento\Customer\Helper\Session\CurrentCustomer;
use ZebrecoPHP\Api as ZebrecoApi;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\DataObjectFactory;

class ListCustomer extends Dashboard
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    
    /**
     * @var \OuterEdge\ZebrecoIntegration\Helper\Data
     */
    protected $zebrecoIntegrationHelper;
    
    protected $_collection;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \OuterEdge\ZebrecoIntegration\Helper\Data $zebrecoIntegrationHelper
     * @param CollectionFactory $collectionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \OuterEdge\ZebrecoIntegration\Helper\Data $zebrecoIntegrationHelper,
        CollectionFactory $collectionFactory,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
        $this->currentCustomer = $currentCustomer;
        $this->zebrecoIntegrationHelper = $zebrecoIntegrationHelper;
        $this->collectionFactory = $collectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->getSupportTickets()) {
            $toolbar = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'customer_support_list.toolbar'
            )->setCollection(
                $this->getSupportTickets()
            );

            $this->setChild('toolbar', $toolbar);
        }
        return parent::_prepareLayout();
    }

    /**
     * Get reviews
     *
     * @return bool|\Magento\Review\Model\ResourceModel\Review\Product\Collection
     */
    public function getSupportTickets()
    {
        if (!($customerId = $this->currentCustomer->getCustomerId())) {
            return false;
        }
        
        if (!$this->_collection) {
            $zebrecoCustomerApi = new ZebrecoApi(
                $this->zebrecoIntegrationHelper->getAccount(),
                $this->zebrecoIntegrationHelper->getUser(),
                $this->zebrecoIntegrationHelper->getPassword(),
                'contact'
            );
            $zebrecoCustomerResults = $zebrecoCustomerApi->getList([
                'query' => [
                    'page'  => '1',
                    'limit' => '1',
                    'q'     => 'email:' . $this->currentCustomer->getCustomer()->getEmail()
                ]
            ]);
            if (!empty($zebrecoCustomerResults) && count($zebrecoCustomerResults['contacts'])) {
                
                $zebrecoCustomerId = $zebrecoCustomerResults['contacts'][0]['id'];
                $zebrecoTicketApi = new ZebrecoApi(
                    $this->zebrecoIntegrationHelper->getAccount(),
                    $this->zebrecoIntegrationHelper->getUser(),
                    $this->zebrecoIntegrationHelper->getPassword(),
                    'ticket'
                );
                $zebrecoTicketResults = $zebrecoTicketApi->getList([
                    'query' => [
                        'page'  => '1',
                        'limit' => '10',
                        'q'     => 'contacts.id:' . $zebrecoCustomerId
                    ]
                ]);
                if (!empty($zebrecoTicketResults) && count($zebrecoTicketResults['tickets'])) {
                    $this->_collection = $this->collectionFactory->create();
                    foreach ($zebrecoTicketResults['tickets'] as $ticket) {
                        $ticketObject = $this->dataObjectFactory->create();
                        $ticketObject->setData($ticket);
                        $this->_collection->addItem($ticketObject);
                    }
                }
            }
                /*$this->_collection = $this->_collectionFactory->create();
                $this->_collection
                    ->addStoreFilter($this->_storeManager->getStore()->getId())
                    ->addCustomerFilter($customerId)
                    ->setDateOrder();*/
        }

        return $this->_collection;
    }

    /**
     * Get review URL
     *
     * @param \Magento\Review\Model\Review $review
     * @return string
     * @since 100.2.0
     */
    public function getSupportTicketUrl($supportTicket)
    {
        return $this->getUrl('support/customer/view', ['id' => $supportTicket->getId()]);
    }

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }
}
