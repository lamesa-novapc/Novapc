<?php

/**
 * Atualizar admin edit form
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Block_Adminhtml_Promotion_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     * @author .
     */
    public function __construct()
    {
        parent::__construct();
        $this->_blockGroup = 'novapc_allnations';
        $this->_controller = 'adminhtml_promotion';
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
        if (Mage::registry('current_promotion') && Mage::registry('current_promotion')->getId()) {
            return Mage::helper('novapc_allnations')->__(
                "Promotion '%s'",
                $this->escapeHtml(Mage::registry('current_promotion')->getName())
            );
        } else {
            return Mage::helper('novapc_allnations')->__('Promotion');
        }
    }
}
