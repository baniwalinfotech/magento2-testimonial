<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Watermark position config source model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Baniwal\Testimonials\Model\Config\Source\Display;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get available options
     *
     * @codeCoverageIgnore
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'left', 'label' => __('Left Block')],
            ['value' => 'right', 'label' => __('Right Block')],

        ];
    }
}
