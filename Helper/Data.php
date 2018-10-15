<?php
namespace Baniwal\Testimonials\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @method string getValue()
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ITEMS_PER_PAGE = 'baniwal/general/postonlist';
    const XML_PATH_POST_SORT_ORDER = 'baniwal/general/post_sorting';
    const XML_PATH_LIST_PAGE_LAYOUT = 'baniwal/general/post_list_layout';
    const XML_PATH_COMMENTS_PER_PAGE = 'baniwal/comments/commentcount';

    const MEDIA_PATH = 'baniwal/blog/image/';

    const MAX_FILE_SIZE = 1048576;

    const MIN_HEIGHT = 50;

    const MAX_HEIGHT = 800;

    const MIN_WIDTH = 50;

    const MAX_WIDTH = 1300;

    protected $_imageSize = [
        'minheight' => self::MIN_HEIGHT,
        'minwidth' => self::MIN_WIDTH,
        'maxheight' => self::MAX_HEIGHT,
        'maxwidth' => self::MAX_WIDTH,
    ];

    public function getStoreConfig($storePath)
    {
        $storeConfig = $this->_scopeConfig->getValue($storePath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $storeConfig;
    }

    public function getPostPerPage()
    {
        return abs((int)$this->_scopeConfig->getValue(self::XML_PATH_ITEMS_PER_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function getCommentsPerPage()
    {
        return abs((int)$this->_scopeConfig->getValue(self::XML_PATH_COMMENTS_PER_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function getSortOrder()
    {
        return abs((int)$this->_scopeConfig->getValue(self::XML_PATH_POST_SORT_ORDER,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function getListPagelayout()
    {
        return abs((int)$this->_scopeConfig->getValue(self::XML_PATH_LIST_PAGE_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    protected $mediaDirectory;

    protected $filesystem;

    protected $httpFactory;

    protected $_fileUploaderFactory;

    protected $_ioFile;

    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\File\Size $fileSize,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->httpFactory = $httpFactory;
        $this->_fileUploaderFactory = $uploaderFactory;
        $this->_ioFile = $ioFile;
        $this->_storeManager = $storeManager;
        $this->_imageFactory = $imageFactory;
        parent::__construct($context);
    }

    public function removeImage($imageFile)
    {
        $io = $this->_ioFile;
        $io->open(['path' => $this->getBaseDir()]);
        if ($io->fileExists($imageFile)) {
            return $io->rm($imageFile);
        }
        return false;
    }

    public function resize(\Baniwal\Blog\Model\Blogpost $item, $width, $height = null)
    {
        if (!$item->getImage()) {
            return false;
        }

        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            return false;
        }
        $width = (int)$width;

        if (!is_null($height)) {
            if ($height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT) {
                return false;
            }
            $height = (int)$height;
        }

        $imageFile = $item->getImage();
        $cacheDir = $this->getBaseDir() . 'cache' . '/' . $width;
        $cacheUrl = $this->getBaseUrlMedia() . 'cache' . '/' . $width . '/';

        $io = $this->_ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(['path' => $cacheDir]);
        if ($io->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }

        try {
            $image = $this->_imageFactory->create($this->getBaseDir() . '/' . $imageFile);
            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageFile);
            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getConfig($configPath)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function resizeThumb(\Baniwal\Blog\Model\Blogpost $item, $width, $height = null)
    {
        if (!$item->getImageThumb()) {
            return false;
        }

        if ($width < self::MIN_WIDTH || $width > self::MAX_WIDTH) {
            return false;
        }
        $width = (int)$width;

        if (!is_null($height)) {
            if ($height < self::MIN_HEIGHT || $height > self::MAX_HEIGHT) {
                return false;
            }
            $height = (int)$height;
        }

        $imageFile = $item->getImageThumb();
        $cacheDir = $this->getBaseDir() . 'cache' . '/' . $width;
        $cacheUrl = $this->getBaseUrlMedia() . 'cache' . '/' . $width . '/';

        $io = $this->_ioFile;
        $io->checkAndCreateFolder($cacheDir);
        $io->open(['path' => $cacheDir]);
        if ($io->fileExists($imageFile)) {
            return $cacheUrl . $imageFile;
        }

        try {
            $image = $this->_imageFactory->create($this->getBaseDir() . '/' . $imageFile);
            $image->resize($width, $height);
            $image->save($cacheDir . '/' . $imageFile);
            return $cacheUrl . $imageFile;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function uploadImage($scope)
    {
        $adapter = $this->httpFactory->create();
        $adapter->addValidator(new \Zend_Validate_File_ImageSize($this->_imageSize));
        $adapter->addValidator(
            new \Zend_Validate_File_FilesSize(['max' => self::MAX_FILE_SIZE])
        );

        if ($adapter->isUploaded($scope)) {
            // validate image
            if (!$adapter->isValid($scope)) {
                throw new \Magento\Framework\Exception\InputException(__('Uploaded image is not valid.'));
            }

            $uploader = $this->_fileUploaderFactory->create(['fileId' => $scope]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $uploader->setAllowCreateFolders(true);

            if ($uploader->save($this->getBaseDir())) {
                return $uploader->getUploadedFileName();
            }
        }
        return false;
    }

    public function getBaseDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(self::MEDIA_PATH);
        return $path;
    }

    public function getBaseUrlMedia()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::MEDIA_PATH;
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

}