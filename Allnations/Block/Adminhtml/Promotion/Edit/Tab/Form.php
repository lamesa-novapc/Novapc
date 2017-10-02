<?php

/**
 * Promotion edit form tab
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      Ultimate Module Creator
 */
class Novapc_Allnations_Block_Adminhtml_Promotion_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return Novapc_Allnations_Block_Adminhtml_Promotion_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('promotion_');
        $form->setFieldNameSuffix('promotion');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'promotion_form',
            array('legend' => Mage::helper('novapc_allnations')->__('Promotion'))
        );
        $fieldset->addType(
            'file',
            Mage::getConfig()->getBlockClassName('novapc_allnations/adminhtml_promotion_helper_file')
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Name'),
                'name'  => 'name',
                'note'	=> $this->__('Name'),
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'id_product',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Product\'s ID in All Nations'),
                'name'  => 'id_product',
                'note'	=> $this->__('ID do Produto'),
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'promo_price',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Promotional Price'),
                'name'  => 'promo_price',
                'note'	=> $this->__('Preço em Promoção'),
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'descrtec',
            'textarea',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Technical description'),
                'name'  => 'descrtec',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'category',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Category'),
                'name'  => 'category',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'manufacturer',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Manufacturer'),
                'name'  => 'manufacturer',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'department',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Department'),
                'name'  => 'department',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'partnumber',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('PartNumber'),
                'name'  => 'partnumber',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'ean',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('EAN'),
                'name'  => 'ean',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'warranty',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Warranty (Months)'),
                'name'  => 'warranty',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'weight',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Weigh (Kg)'),
                'name'  => 'weight',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'resale_price',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Resale Price'),
                'name'  => 'resale_price',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'price_without_st',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Price Without ST'),
                'name'  => 'price_without_st',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'expire_date',
            'date',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Promotion Expire Date'),
                'name'  => 'expire_date',
                'required'  => true,
                'class' => 'required-entry',

            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format'  => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
           )
        );

        $fieldset->addField(
            'available',
            'select',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Available'),
                'name'  => 'available',
                'required'  => true,
                'class' => 'required-entry',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('novapc_allnations')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('novapc_allnations')->__('No'),
                ),
            ),
           )
        );

        $fieldset->addField(
            'pic',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Picture'),
                'name'  => 'pic',

           )
        );

        $fieldset->addField(
            'stock',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Stock'),
                'name'  => 'stock',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'ncm',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('NCM'),
                'name'  => 'ncm',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'width',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Width (CM)'),
                'name'  => 'width',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'height',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Height (CM)'),
                'name'  => 'height',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'depth',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Depth (CM)'),
                'name'  => 'depth',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'active',
            'select',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Active'),
                'name'  => 'active',
                'required'  => true,
                'class' => 'required-entry',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('novapc_allnations')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('novapc_allnations')->__('No'),
                ),
            ),
           )
        );

        $fieldset->addField(
            'subst_tributaria',
            'select',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Incide ICMS ST'),
                'name'  => 'subst_tributaria',
                'required'  => true,
                'class' => 'required-entry',

            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('novapc_allnations')->__('Yes'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('novapc_allnations')->__('No'),
                ),
            ),
           )
        );

        $fieldset->addField(
            'product_origin',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Product\'s Origin'),
                'name'  => 'product_origin',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'available_stock',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Qty Available in Stock'),
                'name'  => 'available_stock',
                'required'  => true,
                'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'sub_category',
            'text',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Sub-Category'),
                'name'  => 'sub_category',
                'required'  => true,
                'class' => 'required-entry',

           )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('novapc_allnations')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('novapc_allnations')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('novapc_allnations')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_promotion')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getPromotionData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getPromotionData());
            Mage::getSingleton('adminhtml/session')->setPromotionData(null);
        } elseif (Mage::registry('current_promotion')) {
            $formValues = array_merge($formValues, Mage::registry('current_promotion')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
