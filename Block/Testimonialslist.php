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
class Testimonialslist extends \Magento\Framework\View\Element\Template
{
    /**
     * @param Template\Context $context
     * @param array $data
     */
    protected $_testimonialsFactory;
    protected $_storeManager;
    protected $customerSession;
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
        $collection = $this->_testimonialsFactory->create()->getCollection();
        $collection->addFieldToFilter('status', 1);
        $collection->setOrder('testimonial_id', 'DESC');
        $this->setCollection($collection);
        $this->pageConfig->getTitle()->set(__('Testimonials'));
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
        if ($this->getCollection()) {
            // create pager block for collection 
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'baniwal.testimonials.record.pager'
            );
            $pager->setAvailableLimit([3 => 3, 6 => 6, 9 => 9, 'all' => 'all']);
            $pager->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $this->setChild('pager', $pager);// set pager block in layout
        }
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
}
