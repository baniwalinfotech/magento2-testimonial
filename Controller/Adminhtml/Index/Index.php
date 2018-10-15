<?php
namespace Baniwal\Testimonials\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Baniwal_Testimonials::testimonials');
        $resultPage->addBreadcrumb(__('Testimonials'), __('Testimonials'));
        $resultPage->addBreadcrumb(__('Manage Testimonials'), __('Manage Testimonials'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Testimonials'));
        return $resultPage;
    }
}
