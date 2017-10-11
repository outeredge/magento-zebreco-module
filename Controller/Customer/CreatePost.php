<?php

namespace OuterEdge\ZebrecoIntegration\Controller\Customer;

use OuterEdge\ZebrecoIntegration\Controller\Customer as CustomerController;
use Magento\Customer\Model\Session;
use OuterEdge\ZebrecoIntegration\Helper\Api;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Psr\Log\LoggerInterface;
use Exception;

class CreatePost extends CustomerController
{
    /**
     * @var Api
     */
    private $api;
    
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var LoggerInterface
     */
    private $logger;
    
    /**
     * DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Api $api
     * @param DataPersistorInterface $dataPersistor
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Api $api,
        DataPersistorInterface $dataPersistor,
        LoggerInterface $logger = null
    ) {
        parent::__construct($context, $customerSession);
        $this->api = $api;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger ?: \Magento\Framework\App\ObjectManager::getInstance()->get(LoggerInterface::class);
    }

    /**
     * Post user question
     *
     * @return Redirect
     */
    public function execute()
    {
        if (!$this->isPostRequest()) {
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again.')
            );
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        try {
            $this->createSupportTicket($this->validatedParams());
            $this->messageManager->addSuccess(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
            $this->getDataPersistor()->clear('support_ticket');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->getDataPersistor()->set('support_ticket', $this->getRequest()->getParams());
        } catch (Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An error occurred while processing your form. Please try again later.')
            );
            $this->getDataPersistor()->set('support_ticket', $this->getRequest()->getParams());
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
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

    /**
     * @param array $post Post data from support ticket form
     * @throws Exception
     * @return void
     */
    private function createSupportTicket($post)
    {
        $contact = $this->api->getContactByEmail($this->customerSession->getCustomer()->getEmail());
        $post['creator'] = $contact['id'];
        $post['posts'][] = [
            'body' => $post['body'],
            'contacts' => [$contact['id']]
        ];
        
        $response = $this->api->getEndpoint('ticket')->create($post);
        if (isset($response['ticket']) && $response['ticket']['id']) {
            return true;
        }
        
        $this->logger->critical($response);
        throw new Exception('Unable to create support ticket via API');
    }

    /**
     * @return bool
     */
    private function isPostRequest()
    {
        /** @var Request $request */
        $request = $this->getRequest();
        return !empty($request->getPostValue());
    }

    /**
     * @return array
     * @throws Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('subject')) === '') {
            throw new LocalizedException(__('Subject is missing'));
        }
        if (trim($request->getParam('body')) === '') {
            throw new LocalizedException(__('Body is missing'));
        }
        if (trim($request->getParam('hideit')) !== '') {
            throw new Exception();
        }
        return $request->getParams();
    }
}
