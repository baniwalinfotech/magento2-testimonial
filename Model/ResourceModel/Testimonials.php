<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Baniwal\Testimonials\Model\ResourceModel;

class Testimonials extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('baniwal_testimonials', 'testimonial_id');
    }

    /* public function checkUrlKey($urlKey)
    {
        $select = $this->getLoadByUrlKeySelect($urlKey, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('baniwal_testimonials.testimonial_id')
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    } */
}
