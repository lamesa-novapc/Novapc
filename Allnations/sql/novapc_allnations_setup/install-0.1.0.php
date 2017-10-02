<?php

/**
 * Allnations module install script
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
$this->startSetup();
$installer = $this;
$attrGroupName = 'All Nations';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');

$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'allnations_sync');

if ($attrIdTest === false) {
    $objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'allnations_sync', array(
        'group'                 => $attrGroupName,
        'sort_order'            => 1,
        'type'                  => 'int',
        'backend'               => '',
        'frontend'              => '',
        'label'                 => 'Sincronizado',
        'note'                  => 'Atributo de controle',
        'input'                 => 'boolean',
        'class'                 => '',
        'source'                => '',
        'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'               => true,
        'required'              => false,
        'user_defined'          => true,
        'default'               => '0',
        'visible_on_front'      => false,
        'unique'                => false,
        'is_configurable'       => false,
        'used_for_promo_rules'  => true
    ));
}

$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, 'allnations_promo');

if ($attrIdTest === false) {
    $objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'allnations_promo', array(
        'group'                 => $attrGroupName,
        'sort_order'            => 2,
        'type'                  => 'int',
        'backend'               => '',
        'frontend'              => '',
        'label'                 => 'Em promoção',
        'note'                  => 'Atributo de controle',
        'input'                 => 'boolean',
        'class'                 => '',
        'source'                => '',
        'global'                => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible'               => true,
        'required'              => false,
        'user_defined'          => true,
        'default'               => '0',
        'visible_on_front'      => false,
        'unique'                => false,
        'is_configurable'       => false,
        'used_for_promo_rules'  => true
    ));
}


$installer->run(
    "CREATE TABLE IF NOT EXISTS `npcallnations_orders` (
        `id` INT AUTO_INCREMENT,
        `order` int,
        `status` int,
        `real_order_id` int,
        `customer` varchar(245),
        `created_at` DATETIME null,
        PRIMARY KEY (ID) 
        )"
);


$installer->run(
    "CREATE TABLE IF NOT EXISTS `npcallnations_attributes` (
      `entity_id` int(11) AUTO_INCREMENT PRIMARY KEY,
      `fabricante` varchar(245) NULL DEFAULT NULL,
      `part_number` varchar(245) NULL DEFAULT NULL,
      `ean` varchar(245) NULL DEFAULT NULL,
      `garantia` varchar(245) NULL DEFAULT NULL,
      `peso` varchar(245) NULL DEFAULT NULL,
      `preco_sem_st` varchar(245) NULL DEFAULT NULL,
      `ncm` varchar(245) NULL DEFAULT NULL,
      `largura` varchar(245) NULL DEFAULT NULL,
      `altura` varchar(245) NULL DEFAULT NULL,
      `profundidade` varchar(245) NULL DEFAULT NULL,
      `subst_tributaria` varchar(245) NULL DEFAULT NULL,
      `origem_produto` varchar(245) NULL DEFAULT NULL)"
);

$installer->run(
    "INSERT INTO `npcallnations_attributes` 
(`entity_id`,
`fabricante`,
`part_number`,
`ean`,
`garantia`,
`peso`,
`preco_sem_st`,
`ncm`,
`largura`,
`altura`,
`profundidade`,
`subst_tributaria`,
`origem_produto`) VALUES
(NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL,
NULL)"
);

$installer->run(
    "CREATE TABLE IF NOT EXISTS `npcallnations_integrations` (
      `entity_id` int(11) AUTO_INCREMENT PRIMARY KEY,
      `integrate_option` varchar(245),
      `updated_at` DATETIME null,
      `first_update` DATETIME NULL 
      )");

$installer->run(
    "INSERT INTO `npcallnations_integrations` (`integrate_option`, `updated_at`, `first_update`) 
    VALUES
    ('Integrar categorias' , NULL, NULL )");

$installer->run("
    INSERT INTO `npcallnations_integrations` (`integrate_option`, `updated_at`, `first_update`)
    VALUES
    ('Integrar Produtos' , NULL, NULL )");

$installer->run("
    INSERT INTO `npcallnations_integrations` (`integrate_option`, `updated_at`, `first_update`)
    VALUES
    ('Atualizar Estoque' , NULL, NULL )");

$table = $this->getConnection()
    ->newTable('npcallnations_promotions')
    ->addColumn(
        'entity_id',
        Varien_Db_Ddl_Table::TYPE_INTEGER,
        null,
        array(
            'identity'  => true,
            'nullable'  => false,
            'primary'   => true,
        ),
        'Promotion ID'
    )
    ->addColumn(
        'name',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Name'
    )
    ->addColumn(
        'id_product',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Product\'s ID in All Nations'
    )
    ->addColumn(
        'promo_price',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Promotional Price'
    )
    ->addColumn(
        'descrtec',
        Varien_Db_Ddl_Table::TYPE_TEXT, 1000,
        array(
            'nullable'  => false,
        ),
        'Technical description'
    )
    ->addColumn(
        'category',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Category'
    )
    ->addColumn(
        'sub_category',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Sub-Category'
    )
    ->addColumn(
        'manufacturer',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Manufacturer'
    )
    ->addColumn(
        'department',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Department'
    )
    ->addColumn(
        'partnumber',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'PartNumber'
    )
    ->addColumn(
        'ean',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'EAN'
    )
    ->addColumn(
        'warranty',
        Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'nullable'  => false,
        ),
        'Warranty (Months)'
    )
    ->addColumn(
        'weight',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Weigh (Kg)'
    )
    ->addColumn(
        'resale_price',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Resale Price'
    )
    ->addColumn(
        'price_without_st',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Price Without ST'
    )
    ->addColumn(
        'expire_date',
        Varien_Db_Ddl_Table::TYPE_DATETIME, 255,
        array(
            'nullable'  => false,
        ),
        'Promotion Expire Date'
    )
    ->addColumn(
        'available',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(
            'nullable'  => false,
        ),
        'Available'
    )
    ->addColumn(
        'pic',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(),
        'Picture'
    )
    ->addColumn(
        'stock',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Stock'
    )
    ->addColumn(
        'ncm',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'NCM'
    )
    ->addColumn(
        'width',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Width (CM)'
    )
    ->addColumn(
        'height',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Height (CM)'
    )
    ->addColumn(
        'depth',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4',
        array(
            'nullable'  => false,
        ),
        'Depth (CM)'
    )
    ->addColumn(
        'active',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(
            'nullable'  => false,
        ),
        'Active'
    )
    ->addColumn(
        'subst_tributaria',
        Varien_Db_Ddl_Table::TYPE_SMALLINT, null,
        array(
            'nullable'  => false,
        ),
        'Incide ICMS ST'
    )
    ->addColumn(
        'product_origin',
        Varien_Db_Ddl_Table::TYPE_TEXT, 255,
        array(
            'nullable'  => false,
        ),
        'Product\'s Origin'
    )
    ->addColumn(
        'available_stock',
        Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,5',
        array(
            'nullable'  => false,
        ),
        'Qty Available in Stock'
    )
    ->addColumn(
        'updated_at',
        Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        null,
        array(),
        'Promotions Modification Time'
    )
    ->setComment('Promotions Table');
$this->getConnection()->createTable($table);

$installer->run("ALTER TABLE `npcallnations_promotions` ADD UNIQUE(`id_product`)");

$this->endSetup();
