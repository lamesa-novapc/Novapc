<?php

/**
 * Promotion resource model
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Model_Resource_Promotion extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author .
     */
    public function _construct()
    {
        $this->_init('novapc_allnations/promotion', 'entity_id');
    }
}
