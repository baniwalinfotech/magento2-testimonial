<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Baniwal\Testimonials\Controller\Index;

use Baniwal\Testimonials\Model\Image as ImageModel;
use Baniwal\Testimonials\Model\Upload;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * Show Contact Us page
     *
     * @return void
     */


    protected $_objectManager;

    protected $imageModel;
    protected $uploadModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ImageModel $imageModel,
        Upload $uploadModel
    ) {
        $this->_objectManager = $context->getObjectManager();
        $this->imageModel = $imageModel;
        $this->uploadModel = $uploadModel;
        parent::__construct($context);
    }

    public function execute()
    {

        try {
            $post = $this->getRequest()->getPostValue();
            $catpage = $this->getRequest()->getParam('customer_page');
            if ($post) {
                $currenttime = time();
                $model = $this->_objectManager->create('Baniwal\Testimonials\Model\Testimonials');

                $model->setData($post);
                $avatarPath = $this->uploadModel->uploadFileAndGetName('avatar_path', $this->imageModel->getBaseDir(),
                    $post);
                $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');

                $configPath = 'testimonials/general/review';
                $value = $scopeConfig->getValue(
                    $configPath,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
                if ($value) {
                    $model->setData('status', 2);
                } else {
                    $model->setData('status', 1);
                }

                $storeConfig = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface');
                $store = $storeConfig->getStore();
                $storeId = $store->getData('store_id');
                $model->setData('storeId', $storeId);
                $model->setData('avatar_path', $avatarPath);
                $model->setData('created_at', $currenttime);
                $model->save();
                if ($catpage != '') {
                    $this->_redirect('testimonials/customer/view');
                } else {
                    $this->_redirect('testimonials/index/view');
                }

                $this->messageManager->addSuccess(__('Testimonials details have been inserted successfully.'));
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

    }
}
