<?php

/**
 * Atualizar admin grid block
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Block_Adminhtml_Promotion_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author .
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('promotionGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('novapc_allnations/promotion')->getCollection();

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'id_produto',
            array(
                'header' => Mage::helper('novapc_allnations')->__('ID na All Nations'),
                'index'  => 'id_product',
                'type'   => 'text',
                'align'  => 'center',
                'width'  => '50px'
            )
        );
        $this->addColumn(
            'name',
            array(
                'header'    => Mage::helper('novapc_allnations')->__('Name'),
                'align'     => 'left',
                'index'     => 'name',
            )
        );
        $store = Mage::app()->getStore();
        $this->addColumn(
            'promo_price',
            array(
                'header' => Mage::helper('novapc_allnations')->__('Preço Promocional'),
                'index'  => 'promo_price',
                'type'   => 'price',
                'width'  => '30px',
                'currency_code' => $store->getBaseCurrency()->getCode(),

            )
        );
        $this->addColumn(
            'expire_date',
            array(
                'header' => Mage::helper('novapc_allnations')->__('Data Final da Promoção'),
                'index'  => 'expire_date',
                'type'   => 'timestamp',
                'width'  => '100px',
                'align'  => 'center'
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'  => Mage::helper('novapc_allnations')->__('Status'),
                'index'   => 'active',
                'align'   => 'center',
                'type'    => 'options',
                'width'   => '50px',
                'options' => array(
                    '1' => Mage::helper('novapc_allnations')->__('Active'),
                    '0' => Mage::helper('novapc_allnations')->__('Inactive'),
                )
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('novapc_allnations')->__('Updated at'),
                'index'     => 'updated_at',
                'align'     => 'center',
                'width'     => '100px',
                'type'      => 'datetime',
            )
        );
        return parent::_prepareColumns();
    }


    /**
     * get the row url
     *
     * @access public
     * @param Novapc_Allnations_Model_Promotion
     * @return string
     * @author .
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * get the grid url
     *
     * @access public
     * @return string
     * @author .
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * after collection load
     *
     * @access protected
     * @return Novapc_Allnations_Block_Adminhtml_Promotion_Grid
     * @author .
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('allnations_promotion');

        $this->getMassactionBlock()->addItem(
            'syncpromo',
            array(
                'label'    => Mage::helper('novapc_allnations')->__('Sincronizar'),
                'url'      => $this->getUrl('*/*/massPromoUpdate')
            )
        );

        return $this;
    }
}
