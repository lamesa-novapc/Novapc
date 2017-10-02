<?php

class Novapc_Allnations_Model_Promotion extends Mage_Core_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY    = 'novapc_allnations_promotion';
    const CACHE_TAG = 'novapc_allnations_promotion';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'novapc_allnations_promotion';

    /**
     * Parameter name in event
     *
     * @var string
     */
    protected $_eventObject = 'promotion';

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
        $this->_init('novapc_allnations/promotion');
    }

    /**
     * before save atualizar
     *
     * @access protected
     * @return Novapc_Allnations_Model_Promotion
     * @author .
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        return $this;
    }

    /**
     * save atualizar relation
     *
     * @access public
     * @return Novapc_Allnations_Model_Promotion
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
