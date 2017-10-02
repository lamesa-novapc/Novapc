<?php

/**
 * Atualizar admin edit tabs
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Block_Adminhtml_Promotion_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Initialize Tabs
     *
     * @access public
     * @author .
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('promotion_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('novapc_allnations')->__('Produto em promoção'));
    }

    /**
     * before render html
     *
     * @access protected
     * @return Novapc_Allnations_Block_Adminhtml_Promotion_Edit_Tabs
     * @author .
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_promotion',
            array(
                'label'   => Mage::helper('novapc_allnations')->__('Promoção'),
                'title'   => Mage::helper('novapc_allnations')->__('Promoção'),
                'content' => $this->getLayout()->createBlock(
                    'novapc_allnations/adminhtml_promotion_edit_tab_form'
                )
                ->toHtml(),
            )
        );
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve atualizar entity
     *
     * @access public
     * @return Novapc_Allnations_Model_Promotion
     * @author .
     */
    public function getPromotion()
    {
        return Mage::registry('current_promotion');
    }
}
