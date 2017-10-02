<?php

/**
 * Atualizar edit form
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Block_Adminhtml_Promotion_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare form
     *
     * @access protected
     * @return Novapc_Allnations_Block_Adminhtml_Promotion_Edit_Form
     * @author .
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'         => 'edit_form',
                'action'     => $this->getUrl(
                    '*/*/save',
                    array(
                        'id' => $this->getRequest()->getParam('id')
                    )
                ),
                'method'     => 'post',
                'enctype'    => 'multipart/form-data'
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
