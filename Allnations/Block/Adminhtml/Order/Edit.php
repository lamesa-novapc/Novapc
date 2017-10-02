<?php


class Novapc_Allnations_Block_Adminhtml_Order_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'novapc_allnations';
        $this->_controller = 'adminhtml_order';
        $this->_removeButton('save');
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('saveandcontinue');
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author .
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_order') && Mage::registry('current_order')->getId()) {
            return Mage::helper('novapc_allnations')->__(
                "All Nations Order #%s",
                $this->escapeHtml(Mage::registry('current_order')->getRealOrderId())
            );
        } else {
            return Mage::helper('novapc_allnations')->__('Order');
        }
    }
}