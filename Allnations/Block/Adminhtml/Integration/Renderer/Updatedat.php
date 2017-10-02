<?php
class Novapc_Allnations_Block_Adminhtml_Integration_Renderer_Updatedat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        if ($value || !empty($value)) {
            return '<div style="background: green; width: 100%; padding: 1px; border-radius: 15px; text-align: center;"><span style="color: white;">'.$value.'</span></div>';
        } else {
            return '<div style="background: red; width: 100%; padding: 1px; border-radius: 15px; text-align: center;">Nunca 
sincronizado</div>';
        }
    }

}