<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Baniwal\Testimonials\Block;

use Magento\Framework\UrlInterface;

/**
 * New products widget
 */
class Widget extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    protected $_template = 'widget/widget.phtml';
    protected $customerSession;
    /**
     * @param Template\Context $context
     * @param array $data
     */
    protected $_testimonialsFactory;
    protected $_storeManager;
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Baniwal\Testimonials\Model\TestimonialsFactory $testimonialsFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_testimonialsFactory = $testimonialsFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->urlBuilder = $context->getUrlBuilder();
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
        //get collection of data 
        $count = $this->getCount();
        $order = $this->getOrder();
        $collection = $this->_testimonialsFactory->create()->getCollection();
        $collection->addFieldToFilter('status', 1);
        if ($order == 'accending') {
            $collection->setOrder('testimonial_id', 'ASC');
        } elseif ($order == 'descending') {
            $collection->setOrder('testimonial_id', 'DESC');
        } elseif ($order == 'random') {
            $collection->getSelect()->order('rand()');
        }
        if (isset($count) && $count != '') {
            $collection->setPageSize($count);
        }

        $this->setCollection($collection);
        $this->pageConfig->getTitle()->set(__('Testimonial List'));
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
    }

    public function getCount()
    {
        return $this->getData('count');
    }

    public function getOffer()
    {
        return $this->getData('offer');
    }

    /**
     * @return string
     */
    // method for get pager html
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
    }

    public function getMediaUrl()
    {

        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . '/testimonials/image';
    }

    public function getcustomerSession()
    {
        if ($this->customerSession->isLoggedIn()) {

            return $this->customerSession->getCustomer()->getData();
        }
    }

    public function getDefaultImage()
    {
        return $this->getViewFileUrl('Baniwal_Testimonials::images/default.jpg');
    }

    public function getCacheLifetime()
    {
        return null;
    }
}
