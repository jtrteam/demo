<?php

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

// add new attribute to customer entity
$setup->addAttribute(
        'customer', 
        'facebook', 
        array(
                'type'              => 'varchar',
                'input'             => 'text',
                'label'             => 'Facebook',
                'global'            => 1,
                'visible'           => 1,
                'required'          => 0,
                'user_defined'      => 1,
                'visible_on_front'  => 1,
                'source'            => 'profile/entity_socialaccounts',
         )
);
$setup->addAttribute(
        'customer', 
        'twitter', 
        array(
                'type'              => 'varchar',
                'input'             => 'text',
                'label'             => 'Twitter',
                'global'            => 1,
                'visible'           => 1,
                'required'          => 0,
                'user_defined'      => 1,
                'visible_on_front'  => 1,
                'source'            => 'profile/entity_socialaccounts',
         )
);
$setup->addAttribute(
        'customer', 
        'pinterest', 
        array(
                'type'              => 'varchar',
                'input'             => 'text',
                'label'             => 'Pinterest',
                'global'            => 1,
                'visible'           => 1,
                'required'          => 0,
                'user_defined'      => 1,
                'visible_on_front'  => 1,
                'source'            => 'profile/entity_socialaccounts',
         )
);
$setup->addAttribute(
        'customer', 
        'instagram', 
        array(
                'type'              => 'varchar',
                'input'             => 'text',
                'label'             => 'Instagram',
                'global'            => 1,
                'visible'           => 1,
                'required'          => 0,
                'user_defined'      => 1,
                'visible_on_front'  => 1,
                'source'            => 'profile/entity_socialaccounts',
         )
);
$setup->addAttribute(
        'customer', 
        'googleplus', 
        array(
                'type'              => 'varchar',
                'input'             => 'text',
                'label'             => 'Google Plus',
                'global'            => 1,
                'visible'           => 1,
                'required'          => 0,
                'user_defined'      => 1,
                'visible_on_front'  => 1,
                'source'            => 'profile/entity_socialaccounts',
         )
);
$installer->endSetup(); 