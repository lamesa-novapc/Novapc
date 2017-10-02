<?php

class Novapc_Allnations_Model_Resource_Attr_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('novapc_allnations/attr');
    }
}