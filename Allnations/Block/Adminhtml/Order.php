<?php
class Novapc_Allnations_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller         = 'adminhtml_order';
        $this->_blockGroup         = 'novapc_allnations';
        parent::__construct();
        $this->_headerText         = Mage::helper('novapc_allnations')->__('Order');
        $this->_removeButton('add');
    }
}