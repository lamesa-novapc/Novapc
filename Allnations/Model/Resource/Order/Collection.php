<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 20/09/2017
 * Time: 09:01
 */ 
class Novapc_Allnations_Model_Resource_Order_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init('novapc_allnations/order');
    }

}