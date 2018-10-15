<?php

namespace Baniwal\Testimonials\Controller;

use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\State;
use Baniwal\Testimonials\Model\TestimonialsFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;
    /**
     * @var ResponseInterface
     */
    protected $_response;
    /**
     * @var
     */
    protected $dispatched;
    /**
     * @var \Baniwal\Faq\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Router constructor.
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param UrlInterface $url
     * @param FaqFactory $faqFactory
     * @param FaqcatFactory $faqcatFactory
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     * @param ResponseInterface $response
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ScopeConfigInterface $scopeConfig
     * @param \Baniwal\Faq\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        UrlInterface $url,
        TestimonialsFactory $testimonialsFactory,
        StoreManagerInterface $storeManager,
        ResponseInterface $response,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $scopeConfig,
        \Baniwal\Testimonials\Helper\Data $dataHelper
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->url = $url;
        $this->_dataHelper = $dataHelper;
        $this->testimonialsFactory = $testimonialsFactory;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->response = $response;
        $this->_response = $response;
        $this->scopeConfig = $scopeConfig;
    }

    public function getPageUrl()
    {
        return $this->scopeConfig->getValue('testimonials/general/list_url');
    }

    /**
     * @param RequestInterface $request
     * @return null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->dispatched) {

            $urlKey = trim($request->getPathInfo(), '/');
            $origUrlKey = $urlKey;

            /** @var Object $condition */
            $condition = new DataObject(['url_key' => $urlKey, 'continue' => true]);
            $this->eventManager->dispatch(
                'baniwal_testimonials_controller_router_match_before',
                ['router' => $this, 'condition' => $condition]
            );

            $urlKey = $condition->getUrlKey();
            if ($condition->getRedirectUrl()) {
                $this->response->setRedirect($condition->getRedirectUrl());
                $request->setDispatched(true);
                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Redirect',
                    ['request' => $request]
                );
            }

            if (!$condition->getContinue()) {
                return null;
            }
            $identifier = trim($request->getPathInfo(), '/');

            if ($identifier == $this->getPageUrl()) {
                $request->setModuleName('testimonials')
                    ->setControllerName('index')
                    ->setActionName('view');

                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);
                $this->dispatched = true;

                return $this->actionFactory->create(
                    'Magento\Framework\App\Action\Forward',
                    ['request' => $request]
                );
            }

            $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $origUrlKey);
            $request->setDispatched(true);
            $this->dispatched = true;

            return $this->actionFactory->create(
                'Magento\Framework\App\Action\Forward',
                ['request' => $request]
            );
        }
        return null;
    }
}
