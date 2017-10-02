<?php
class Novapc_Allnations_Model_Resource_Integration_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_joinedFields = array();

    protected function _construct()
    {
        parent::_construct();
        $this->_init('novapc_allnations/integration');
    }

    protected function _toOptionArray($valueField='entity_id', $labelField='name', $additional=array())
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }


    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }

    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}