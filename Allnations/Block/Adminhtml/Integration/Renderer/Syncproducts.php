<?php
class Novapc_Allnations_Block_Adminhtml_Integration_Renderer_Syncproducts extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        if ($value || !empty($value)) {
            return '<div style="font-size: 15px; width: 100%; padding: 1px; border-radius: 15px; text-align: left;">'.$value.'</div>';
        } else {
            return '<div style="background: red; width: 100%; padding: 1px; border-radius: 15px; text-align: center;">Sincronização Indisponivel</div>';
        }
    }

}