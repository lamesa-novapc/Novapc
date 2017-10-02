<?php
class Novapc_Allnations_Block_Adminhtml_Order_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        if ($value || !empty($value)) {
            if ($value == '1') {
                return '<span style="font-size: 15px">Pedido Pendente</span>';
            } elseif ($value == '2') {
                return '<div style="background: limegreen; width: 100%; padding: 1px; border-radius: 15px;"><span 
style="color: black; font-size: 15px">Pedido Confirmado</span></div>';
            } elseif ($value == '3') {
                return '<div style="background: green; width: 100%; padding: 1px; border-radius: 15px;"><span 
style="color: white; font-size: 15px">Pedido Gerado</span></div>';
            } elseif ($value == '4') {
                return '<div style="background: red; width: 100%; padding: 1px; border-radius: 15px;"><span 
style="color: black; font-size: 15px">Pedido Cancelado</span></div>';
            }
        } else {
            return '<div style="width: 100%; padding: 1px; border-radius: 15px;"><span style="color: 
black; font-size: 15px">Sem Informações</div></div>';
        }
    }

}