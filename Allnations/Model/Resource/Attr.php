<?php
class Novapc_Allnations_Model_Resource_Attr extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('novapc_allnations/attr', 'entity_id');
    }
}