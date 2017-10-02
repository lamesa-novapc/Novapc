<?php

/**
 * Atualizar admin grid block
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Block_Adminhtml_Integration_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $this->setFilterVisibility(false);
        $this->setId('integrationGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setPagerVisibility(false);
    }

    public function _prepareLayout()
    {
        $this->unsetChild('reset_filter_button');
        $this->unsetChild('search_button');
    }


    protected function _prepareCollection()
    {
        $collection = Mage::getModel('novapc_allnations/integration')->getCollection();

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'integrate_option',
            array(
                'header'    => Mage::helper('novapc_allnations')->__('Integrate'),
                'index'     => 'integrate_option',
                'width'     => '200px',
                'type'      => 'text',
                'filter'    => false,
                'renderer'  => 'Novapc_Allnations_Block_Adminhtml_Integration_Renderer_Syncproducts',
                'sortable'  => false
            )
        );
        $this->addColumn(
            'updated_at',
            array(
                'header'    => Mage::helper('novapc_allnations')->__('Updated at'),
                'index'     => 'updated_at',
                'width'     => '80px',
                'type'      => 'datetime',
                'filter'    => false,
                'renderer'  => 'Novapc_Allnations_Block_Adminhtml_Integration_Renderer_Updatedat',
                'sortable'  => false
            )
        );
        $this->addColumn(
            'first_update',
            array(
                'header'    => Mage::helper('novapc_allnations')->__('First update'),
                'index'     => 'first_update',
                'width'     => '80px',
                'type'      => 'datetime',
                'filter'    => false,
                'renderer'  => 'Novapc_Allnations_Block_Adminhtml_Integration_Renderer_Createdat',
                'sortable'  => false
            )
        );
        return parent::_prepareColumns();
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


    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('allnations_integration');

        $this->getMassactionBlock()->addItem(
            'integratecat',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Integrar Categorias'),
                'url'   => $this->getUrl('*/*/massCategory')
            )
        )
        ->addItem(
            'integrateprod',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Integrar Produtos'),
                'url'   => $this->getUrl('*/*/massIntegrateProduct')
            )
        )
        ->addItem(
            'updatestock',
            array(
                'label' => Mage::helper('novapc_allnations')->__('Atualizar Estoque'),
                'url'   => $this->getUrl('*/*/massUpdateStock')
            )
        )->setUseSelectAll(false);

        return $this;
    }
}
