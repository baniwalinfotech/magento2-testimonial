<?php

namespace Baniwal\Testimonials\Block\Top;

class Link extends \Magento\Framework\View\Element\Html\Link
{

    protected $httpContext;

    protected $_customerUrl;

    protected $_postDataHelper;
    public $dataHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Baniwal\Testimonials\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_customerUrl = $customerUrl;
        $this->dataHelper = $dataHelper;
    }
    public function getHref()
    {
        $baseUrl = $this->dataHelper->getBaseUrl();
        $listUrl = $this->dataHelper->getStoreConfig('testimonials/general/list_url');
        return $this->getUrl($listUrl);
    }
    public function getLabel()
    {
        return $this->dataHelper->getStoreConfig('testimonials/general/top_menu_title');
    }
}
