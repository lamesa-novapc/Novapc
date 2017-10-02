<?php

/**
 * Promotion admin controller
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Adminhtml_Allnations_PromotionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init the atualizar
     *
     * @access protected
     * @return Novapc_Allnations_Model_Promotion
     */
    protected function _initPromotion()
    {
        $promotionId  = (int) $this->getRequest()->getParam('id');
        $promotion    = Mage::getModel('novapc_allnations/promotion');
        if ($promotionId) {
            $promotion->load($promotionId);
        }
        Mage::register('current_promotion', $promotion);
        return $promotion;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author .
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('novapc_allnations')->__('All Nations'))
             ->_title(Mage::helper('novapc_allnations')->__('Promotions'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author .
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit atualizar - action
     *
     * @access public
     * @return void
     * @author .
     */
    public function editAction()
    {
        $promotionId    = $this->getRequest()->getParam('id');
        $promotion      = $this->_initPromotion();
        if ($promotionId && !$promotion->getId()) {
            $this->_getSession()->addError(
                Mage::helper('novapc_allnations')->__('This no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getPromotionData(true);
        if (!empty($data)) {
            $promotion->setData($data);
        }
        Mage::register('promotion_data', $promotion);
        $this->loadLayout();
        $this->_title(Mage::helper('novapc_allnations')->__('All Nations'))
             ->_title(Mage::helper('novapc_allnations')->__('Promotions'));
        if ($promotion->getId()) {
            $this->_title($promotion->getName());
        } else {
            $this->_title(Mage::helper('novapc_allnations')->__('Promotion'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new atualizar action
     *
     * @access public
     * @return void
     * @author .
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save atualizar - action
     *
     * @access public
     * @return void
     * @author .
     */


    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('novapc_allnations/promotion');
    }

    public function massPromoUpdateAction()
    {
        # Força o load da página em modo ADMIN
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        # Hoje
        $today = Mage::getModel('core/date');

        # Carrega os atributos selecionados na configuração
        $attr = Mage::getModel('novapc_allnations/attr')->load(1, 'entity_id');
        $attr = array(
            $attr->getData('fabricante'),
            $attr->getData('part_number'),
            $attr->getData('ean'),
            $attr->getData('garantia'),
            $attr->getData('peso'),
            $attr->getData('preco_sem_st'),
            $attr->getData('ncm'),
            $attr->getData('largura'),
            $attr->getData('altura'),
            $attr->getData('profundidade'),
            $attr->getData('subst_tributaria'),
            $attr->getData('origem_produto'),
        );

        # Verifica qual o atributo que representa o ID da All Nations
        $control = Mage::getStoreConfig('allnations/general/id_allnations', Mage::app()->getStore());

        # Pega os IDs selecionados pela MassAction
        $filter = $this->getRequest()->getParams();
        $filter = $filter['allnations_promotion'];


        # Pega os produtos em promoção, filtrados pelos IDs selecionados acima
        $promoModel = Mage::getModel('novapc_allnations/promotion');

        $promo2 = $promoModel->getCollection()
            ->addFieldToFilter('entity_id', array('in' => $filter));
        foreach ($promo2 as $prom) {
            $pIds[] = $prom->getData('id_product');
        }


        $stock = Mage::getModel('cataloginventory/stock_item');
        $product = Mage::getModel('catalog/product');

        $products = $product->getCollection()
            ->addAttributeToFilter($control, array('in' => $pIds))
            ->addAttributeToSelect('*');

        # Atualiza os produtos em promoção
        if ($products->getSize() > 0) {
            foreach ($products as $pr) {
                $attrText = $pr->getData($control);
                $promoeach = $promoModel->getCollection()
                    ->addFieldToFilter('id_product', array('eq' => $attrText))
                    ->addFieldToSelect('*');

                if (($pr->getData('allnations_promo')) == '0' and ($pr->getData('allnations_sync') == '1')){
                    foreach ($promoeach as $p) {
                        $loaded = $promoModel->load($p->getId());

                        $pr->setName($loaded->getName());
                        $pr->setDescription($loaded->getDescrtec());
                        $pr->setAvailable($loaded->getAvailable());
                        $pr->setStatus($loaded->getActive());

                        $setData = $product->loadByAttribute($control, $p->getIdProduct());
                        $setData->addAttributeUpdate($attr['0'], $p->getData('manufacturer'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['1'], $p->getData('partnumber'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['2'], $p->getData('ean'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['3'], $p->getData('warranty'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['4'], $p->getData('weight'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['5'], $p->getData('price_without_st'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['6'], $p->getData('ncm'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['7'], $p->getData('width'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['8'], $p->getData('height'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['9'], $p->getData('depth'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['10'], $p->getData('subst_tributaria'), $setData->getStoreId());
                        $setData->addAttributeUpdate($attr['11'], $p->getData('product_origin'), $setData->getStoreId());
                        $setData->save();


                        # Colocando os valores no campo special price e nas datas from e to
                        $pr->setSpecialPrice($loaded->getData('promo_price'));
                        $from = $today->gmtDate('Y-m-d');

                        $pr->setSpecialFromDate($from);
                        $pr->setSpecialFromDateIsFormated(true);

                        $dat = $today->date('Y-m-d', $loaded->getData('expire_date'));
                        $toDate = new Zend_Date($dat);
                        $toDate->subDay('1');
                        $pr->setSpecialToDate($toDate);
                        $pr->setSpecialToDateIsFormated(true);

                        $pr->setAllnationsPromo(1);

                        $pr->save();

                        # Carrega o estoque pelo produto
                        $stoc = $stock->loadByProduct($pr);

                        # Verifica se tem estoque ou não
                        $qty = $loaded->getData('available_stock');
                        $stoc->setQty($qty);

                        if ($qty > 0) {
                            $stoc->setIsInStock(1);
                        } else {
                            $stoc->setIsInStock(0);
                        }
                        $stoc->save();
                    }
                }
            }
        }
        Mage::getSingleton('core/session')->addSuccess(Mage::helper('novapc_allnations')->__('Sincronização Completa.'));
        $this->_redirect('*/*/');
    }
}