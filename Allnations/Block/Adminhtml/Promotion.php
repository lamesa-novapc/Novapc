<?php


class Novapc_Allnations_Block_Adminhtml_Promotion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @author .
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_promotion';
        $this->_blockGroup         = 'novapc_allnations';
        parent::__construct();
        $this->_headerText         = Mage::helper('novapc_allnations')->__('Promoções');
        $this->_removeButton('add');
    }
}
