<?php

class Novapc_Allnations_Model_System_Config_Source_Dropdown_Category
{
    public function toOptionArray()
    {
        $productAttrs = Mage::getModel('catalog/category')
            ->getCollection()
            ->addAttributeToSelect('*');

        $retornArray = array('not_selected' => 'Selecione a categoria');

        foreach ($productAttrs as $productAttr) {
            $retornArray[] = array(
                'value' => $productAttr->getId(),
                'label' => $productAttr->getName()
            );
        }

        return $retornArray;
    }
}