<?php

namespace OuterEdge\ZebrecoIntegration\Block\Customer;

use Magento\Customer\Block\Account\Dashboard;
use OuterEdge\ZebrecoIntegration\Helper\Api;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Framework\Data\CollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\DataObject;

class ListCustomer extends Dashboard
{
    /**
     * @var Api
     */
    protected $api;
    
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;
    
    /**
     * @var Collection
     */
    protected $collection;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @param Context $context
     * @param Api $api
     * @param Session $customerSession
     * @param SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param CurrentCustomer $currentCustomer
     * @param CollectionFactory $collectionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Api $api,
        Session $customerSession,
        SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        CurrentCustomer $currentCustomer,
        CollectionFactory $collectionFactory,
        DataObjectFactory $dataObjectFactory,
        array $data = []
    ) {
        $this->api = $api;
        $this->currentCustomer = $currentCustomer;
        $this->collectionFactory = $collectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
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
     * Get support tickets
     *
     * @return bool|Collection
     */
    public function getSupportTickets()
    {
        if (!($customerId = $this->currentCustomer->getCustomerId())) {
            return false;
        }
        
        if (!$this->collection) {
            
            $contact = $this->api->getContactByEmail($this->currentCustomer->getCustomer()->getEmail());
            if ($contact) {
                
                $zebrecoTickets = $this->api->getEndpoint('ticket')->getList([
                    'query' => [
                        'page'  => '1',
                        'limit' => '10',
                        'q'     => 'contacts.id:' . $contact['id']
                    ]
                ]);
                if (!empty($zebrecoTickets) && count($zebrecoTickets['tickets'])) {
                    
                    $this->collection = $this->collectionFactory->create();
                    
                    foreach ($zebrecoTickets['tickets'] as $ticket) {
                        $ticketObject = $this->dataObjectFactory->create();
                        $ticketObject->setData($ticket);
                        $this->collection->addItem($ticketObject);
                    }
                }
            }
        }
        return $this->collection;
    }

    /**
     * Get review URL
     *
     * @param \Magento\Review\Model\Review $review
     * @return string
     * @since 100.2.0
     */
    public function getSupportTicketUrl($supportTicket = null)
    {
        if ($supportTicket) {
            return $this->getUrl('support/customer/view', ['id' => $supportTicket->getId()]);
        }
        return $this->getUrl('support/customer/create');
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
