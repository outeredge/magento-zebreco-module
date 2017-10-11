<?php

namespace OuterEdge\ZebrecoIntegration\Block\Customer;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use OuterEdge\ZebrecoIntegration\Helper\Api;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Data\CollectionFactory;
use Edge\Markdown\Markdown;
use IntlDateFormatter;

class View extends Template
{
    /**
     * @var Api 
     */
    protected $api;
    
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;
    
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    
    /**
     * @var null|DataObject
     */
    protected $supportTicket = null;
    
    /**
     * @param Context $context
     * @param Api $api
     * @param DataObjectFactory $dataObjectFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Api $api,
        DataObjectFactory $dataObjectFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->api = $api;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    
    /**
     * Get support ticket as object
     * 
     * @return null|DataObject
     */
    public function getSupportTicket()
    {
        if (!$this->supportTicket) {
            $supportTicket = $this->api->getEndpoint('ticket')->get($this->getSupportTicketId());
            if ($supportTicket && isset($supportTicket['ticket'])) {
                
                $this->supportTicket = $this->dataObjectFactory->create();
                $this->supportTicket->setData($supportTicket['ticket']);
                
                $postsArray = [];
                $posts = $this->collectionFactory->create();
                foreach ($supportTicket['ticket']['posts'] as $post) {
                    $postObject = $this->dataObjectFactory->create();
                    $postObject->setData($post);
                    array_unshift($postsArray, $postObject);
                }
                foreach ($postsArray as $post) {
                    $posts->addItem($post);
                }
                $this->supportTicket->setPosts($posts);
            }
        }
        return $this->supportTicket;
    }
    
    /**
     * Get support ticket status
     * 
     * @return string
     */
    public function getStatus()
    {
        switch ($this->getSupportTicket()->getStatus()) {
            case 'open':
                return 'Open';
            case 'awaiting-reply':
                return 'Awaiting Reply';
            case 'closed':
                return 'Closed';
        }
    }
    
    /**
     * Get date and time for post
     * 
     * @return string
     */
    public function dateTimeFormat($date)
    {
        return $this->formatDate($date, IntlDateFormatter::MEDIUM) . '<br>' . $this->formatTime($date, IntlDateFormatter::SHORT);
    }
    
    /**
     * Parse markdown body
     * 
     * @return string
     */
    public function parseMarkdown($content)
    {
        $markdown = new Markdown();
        return $markdown->transform($content);
    }
    
    /**
     * Get support ticket id from url
     * 
     * @return false|int
     */
    protected function getSupportTicketId()
    {
        return $this->getRequest()->getParam('id', false);
    }
}