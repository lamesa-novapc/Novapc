<?php

class Novapc_Allnations_Block_Adminhtml_Order_Renderer_Client extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($value || !empty($value)) {
                return '<span style="font-size: 15px">' . $value . '</span>';
        } else {
            return '<span style="font-size: 15px">Sem Informações</SPAN>';
        }
    }
}