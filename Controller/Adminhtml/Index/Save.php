<?php
namespace Baniwal\Testimonials\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Baniwal\Testimonials\Model\Image as ImageModel;
use Baniwal\Testimonials\Model\Upload;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */

    protected $imageModel;
    protected $uploadModel;

    public function __construct(Action\Context $context, ImageModel $imageModel, Upload $uploadModel)
    {
        $this->imageModel = $imageModel;
        $this->uploadModel = $uploadModel;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Baniwal\Testimonials\Model\Testimonials');

            $id = $this->getRequest()->getParam('testimonial_id');
            if ($id) {
                $model->load($id);
            }

            $model->setData($data);
            $stores = implode(",", $data['stores']);
            $model->setData('storeId', $stores);
            $avatarPath = $this->uploadModel->uploadFileAndGetName('avatar_path', $this->imageModel->getBaseDir(),
                $data);
            $model->setData('avatar_path', $avatarPath);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Testimonial has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit',
                        ['testimonial_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the entry.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit',
                ['testimonial_id' => $this->getRequest()->getParam('testimonial_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
