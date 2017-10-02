<?php

/**
 * Admin search model
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Model_Adminhtml_Search_Promotion extends Varien_Object
{
    /**
     * Load search results
     *
     * @access public
     * @return Novapc_Allnations_Model_Adminhtml_Search_Promotion
     * @author .
     */
    public function load()
    {
        $arr = array();
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        $collection = Mage::getResourceModel('novapc_allnations/promotion_collection')
            ->addFieldToFilter('name', array('like' => $this->getQuery().'%'))
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();
        foreach ($collection->getItems() as $promotion) {
            $arr[] = array(
                'id'          => 'promotion/1/'.$promotion->getId(),
                'type'        => Mage::helper('novapc_allnations')->__('Promotions'),
                'name'        => $promotion->getName(),
                'description' => $promotion->getName(),
                'url' => Mage::helper('adminhtml')->getUrl(
                    '*/allnations_promotion/edit',
                    array('id'=>$promotion->getId())
                ),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
