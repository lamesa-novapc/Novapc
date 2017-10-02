<?php


class Novapc_Allnations_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('orderGrid');
        $this->setDefaultSort('order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('novapc_allnations/order')->getCollection();

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'real_order_id',
            array(
                'header' => Mage::helper('novapc_allnations')->__('Order ID'),
                'index'  => 'real_order_id',
                'type'   => 'text',
                'align'  => 'center',
                'width'  => '70px',
                'renderer'  => 'Novapc_Allnations_Block_Adminhtml_Order_Renderer_Id',
                'sortable' => false
            )
        );
        $this->addColumn(
            'status',
            array(
                'header'    => Mage::helper('novapc_allnations')->__('Status'),
                'align'     => 'center',
                'index'     => 'status',
                'renderer'  => 'Novapc_Allnations_Block_Adminhtml_Order_Renderer_Status',
                'width'     => '120px',
                'sortable' => false
            )
        );
        $this->addColumn(
            'cliente',
            array(
                'header' => Mage::helper('novapc_allnations')->__('Cliente'),
                'align'  => 'left',
                'index'  => 'customer',
                'width'  => '300px',
                'renderer' => 'Novapc_Allnations_Block_Adminhtml_Order_Renderer_Client',
                'sortable' => false
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header'   => Mage::helper('novapc_allnations')->__('Criado em'),
                'align'    => 'center',
                'index'    => 'created_at',
                'width'    => '150px',
                'renderer' => 'Novapc_Allnations_Block_Adminhtml_Order_Renderer_Id',
                'sortable' => false
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
     * @return .
     * @author .
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('real_order_id');
        $this->getMassactionBlock()->setFormFieldName('allnations_order');

        $this->getMassactionBlock()->addItem(
            'confirmOrder',
            array(
                'label'    => Mage::helper('novapc_allnations')->__('Confirmar'),
                'url'      => $this->getUrl('*/*/massConfirmOrder')
            )
        );
        $this->getMassactionBlock()->addItem(
            'cancelOrder',
            array(
                'label'    => Mage::helper('novapc_allnations')->__('Cancelar'),
                'url'      => $this->getUrl('*/*/massCancelOrder')
            )
        );
        $this->getMassactionBlock()->addItem(
            'excludeOrd',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Excluir'),
                'url'   => $this->getUrl('*/*/massExcludeOrd')
            )
        );

        return $this;
    }
}