<?php
class Novapc_Allnations_Model_Integration extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'novapc_allnations_integration';
    const CACHE_TAG = 'novapc_allnations_integration';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'novapc_allnations_integration';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'integration';

    /**
     * constructor
     *
     * @access public
     * @return void
     * @author .
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('novapc_allnations/integration');
    }


    /**
     * save promotions relation
     *
     * @access public
     * @return Novapc_Allnations_Model_Integration
     * @author .
     */
    protected function _afterSave()
    {
        return parent::_afterSave();
    }

    /**
     * get default values
     *
     * @access public
     * @return array
     * @author .
     */
    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        return $values;
    }
}