<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Baniwal\Testimonials\Block;

use Magento\Framework\View\Element\Template;


/**
 * Main contact form block
 */
class Testimonialform extends Template
{

    /**
     * @param Template\Context $context
     * @param array $data
     */

    protected $customerSession;

    /**
     * @param ...
     * @param \Magento\Backend\Model\Auth\Session $backendAuthSession
     * @param ...
     */
    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->customerSession = $customerSession;

    }

    public function getcustomerSession()
    {
        if ($this->customerSession->isLoggedIn()) {

            return $this->customerSession->getCustomer()->getData();
        }
    }

}
