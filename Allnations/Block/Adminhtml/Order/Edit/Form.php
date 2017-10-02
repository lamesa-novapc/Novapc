<?php


class Novapc_Allnations_Block_Adminhtml_Order_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('order_');
        $form->setFieldNameSuffix('order');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'order_form',
            array('legend' => Mage::helper('novapc_allnations')->__('Order'))
        );
        $fieldset->addType(
            'file',
            Mage::getConfig()->getBlockClassName('novapc_allnations/adminhtml_order_helper_file')
        );

        $fieldset->addField(
            'order',
            'text',
            array(
                'label'  => Mage::helper('novapc_allnations')->__('Order'),
                'name'   => 'order'

            )
        );

        $fieldset->addField(
            'real_order_id',
            'text',
            array(
                'label'     => Mage::helper('novapc_allnations')->__('Order ID'),
                'name'      => 'real_order_id',
                'required'  => true,
                'class'     => 'required-entry',
            )
        );

        $fieldset->addField(
        'status',
        'text',
            array(
                'label'     => Mage::helper('novapc_allnations')->__('Status'),
                'name'      => 'status',
                'required'  => true,
                'class'     => 'required-entry',
            )
        );

        $fieldset->addField(
            'customer',
            'text',
            array(
                'label'     => Mage::helper('novapc_allnations')->__('Customer'),
                'name'      => 'customer',
                'required'  => true,
                'class'     => 'required-entry',
            )
        );

        $formValues = Mage::registry('current_order')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getOrderData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getOrderData());
            Mage::getSingleton('adminhtml/session')->getOrderData(null);
        } elseif (Mage::registry('current_order')) {
            $formValues = array_merge($formValues, Mage::registry('current_order')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}