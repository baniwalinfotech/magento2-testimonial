<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Baniwal\Testimonials\Block;

use Magento\Framework\UrlInterface;

/**
 * Main contact form block
 */
class Testimonialssidebar extends \Magento\Framework\View\Element\Template
{
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
        \ Baniwal\Testimonials\Model\TestimonialsFactory $testimonialsFactory,
        array $data = []
    ) {
        $this->_testimonialsFactory = $testimonialsFactory;
        $this->_storeManager = $context->getStoreManager();
        $this->urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
        //get collection of data 
        $collection = $this->_testimonialsFactory->create()->getCollection();
        $collection->addFieldToFilter('status', 1);
        $collection->setOrder('testimonial_id', 'DESC');
        $this->setCollection($collection);
        $this->pageConfig->getTitle()->set(__('Testimonial List'));
    }

    public function sidebarCollection()
    {
        $collection = $this->_testimonialsFactory->create()->getCollection();
        $collection->addFieldToFilter('status', 1);
        $collection->setOrder('testimonial_id', 'DESC');
        return $collection;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        return $this;
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

    public function getMediabaseUrl()
    {

        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
    }

    public function getDefaultImage()
    {
        return $this->getViewFileUrl('Baniwal_Testimonials::images/default.jpg');
    }

}
