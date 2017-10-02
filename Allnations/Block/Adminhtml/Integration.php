<?php


class Novapc_Allnations_Block_Adminhtml_Integration extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * constructor
     *
     * @access public
     * @author .
     */
    public function __construct()
    {
        $this->_controller         = 'adminhtml_integration';
        $this->_blockGroup         = 'novapc_allnations';
        parent::__construct();
        $this->_headerText         = Mage::helper('novapc_allnations')->__('Integration');
        $this->_removeButton('add');
    }
}
