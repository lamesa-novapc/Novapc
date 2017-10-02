<?php
class Novapc_Allnations_Model_Resource_Integration extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * constructor
     *
     * @access public
     * @author .
     */
    public function _construct()
    {
        $this->_init('novapc_allnations/integration', 'entity_id');
    }
}