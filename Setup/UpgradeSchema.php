<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Baniwal\Testimonials\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $setup->getConnection()->addColumn(
                $setup->getTable('baniwal_testimonials'),
                'testimonial',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'Tetimonial comment'
                ]
            );

            $setup->getConnection()->dropColumn($setup->getTable('baniwal_testimonials'), 'website');
            $setup->getConnection()->dropColumn($setup->getTable('baniwal_testimonials'), 'company');
            $setup->getConnection()->dropColumn($setup->getTable('baniwal_testimonials'), 'address');

            $setup->getConnection()->addColumn(
                $setup->getTable('baniwal_testimonials'),
                'customer_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => '',
                    'comment' => 'current customer'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('baniwal_testimonials'),
                'storeId',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 10,
                    'nullable' => false,
                    'default' => 0,
                    'comment' => 'Store Id'
                ]
            );
        }
        $setup->endSetup();
    }
}
