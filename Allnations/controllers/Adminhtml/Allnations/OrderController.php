<?php

class Novapc_Allnations_Adminhtml_Allnations_OrderController extends Mage_Adminhtml_Controller_Action
{
    protected function _initOrder()
    {
        $orderId  = (int) $this->getRequest()->getParam('id');
        $order    = Mage::getModel('novapc_allnations/order');
        if ($orderId) {
            $order->load($orderId);
        }
        Mage::register('current_order', $order);
        return $order;
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
            ->_title(Mage::helper('novapc_allnations')->__('Order'));
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

    public function editAction()
    {
        $orderId    = $this->getRequest()->getParam('order');
        $order      = $this->_initOrder();
        if ($orderId && !$order->getId()) {
            $this->_getSession()->addError(
                Mage::helper('novapc_allnations')->__('This no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getorderData(true);
        if (!empty($data)) {
            $order->setData($data);
        }
        Mage::register('order_data', $order);
        $this->loadLayout();
        $this->_title(Mage::helper('novapc_allnations')->__('All Nations'))
            ->_title(Mage::helper('novapc_allnations')->__('Orders'));
        if ($order->getId()) {
            $this->_title($order->getName());
        } else {
            $this->_title(Mage::helper('novapc_allnations')->__('Order'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('novapc_allnations/order');
    }

    public function massConfirmOrderAction()
    {
        try {
            # Força o load da página em modo ADMIN
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            # Pega os IDs selecionados pela MassAction
            $filter = $this->getRequest()->getParams();
            $filter = $filter['allnations_order'];

            # Model dos pedidos
            $ANorders = Mage::getModel('novapc_allnations/order');

            foreach ($filter as $single) {
                $order = $ANorders->load($single);
                $insert = Mage::helper('novapc_allnations')->confirmOrder($order->getOrder());

                if ($insert['httpCode'] == 200) {
                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('novapc_allnations')->__('Pedidos confirmados.'));
                    $this->_redirect('*/*/');
                } elseif ($insert['httpCode'] == 500) {
                    Mage::getSingleton('core/session')
                        ->addError(Mage::helper('novapc_allnations')->__('Http Code: 500. Usuario e/ou senha incorretos. Caso o erro persista, contate o suporte'));
                    $this->_redirect('*/*/');
                } else {
                    Mage::getSingleton('core/session')
                        ->addError(Mage::helper('novapc_allnations')->__('Um erro inesperado ocorreu. Contate o suporte'));
                    $this->_redirect('*/*/');
                }
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')
                ->addError(Mage::helper('novapc_allnations')->__('Exception: Um erro inesperado ocorreu. Contate o suporte'));
            $this->_redirect('*/*/');
        }
    }

    public function massCancelOrderAction()
    {
        try {
            # Força o load da página em modo ADMIN
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            # Pega os IDs selecionados pela MassAction
            $filter = $this->getRequest()->getParams();
            $filter = $filter['allnations_order'];
            $ANorder = Mage::getModel('novapc_allnations/order');

            foreach ($filter as $single) {
                $order = $ANorder->load($single);
                $cancel = Mage::helper('novapc_allnations')->cancelOrder($order->getOrder());
            }

            if ($cancel['httpCode'] == 200) {
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('novapc_allnations')->__('Pedidos cancelados.'));
                $this->_redirect('*/*/');
            } elseif ($cancel['httpCode'] == 500) {
                Mage::getSingleton('core/session')
                    ->addError(Mage::helper('novapc_allnations')->__('Http Code: 500. Usuario e/ou senha incorretos. Caso o erro persista, contate o suporte'));
                $this->_redirect('*/*/');
            } else {
                Mage::getSingleton('core/session')
                    ->addError(Mage::helper('novapc_allnations')->__('Um erro inesperado ocorreu. Contate o suporte'));
                $this->_redirect('*/*/');
            }


        } catch (Exception $e) {
            Mage::getSingleton('core/session')
                ->addError(Mage::helper('novapc_allnations')->__('Exception: Um erro inesperado ocorreu. Contate o suporte'));
            $this->_redirect('*/*/');
        }
    }

    public function massExcludeOrdAction()
    {
        try {
            # Força o load da página em modo ADMIN
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            # Pega os IDs selecionados pela MassAction
            $filter = $this->getRequest()->getParams();
            $filter = $filter['allnations_order'];
            $ANorder = Mage::getModel('novapc_allnations/order');

            foreach ($filter as $single) {
                $ANorder->load($single)->delete();
            }

            Mage::getSingleton('core/session')->addSuccess(Mage::helper('novapc_allnations')->__('Pedidos excluidos.'));
            $this->_redirect('*/*/');

        } catch (Exception $e) {
            Mage::getSingleton('core/session')
                ->addError(Mage::helper('novapc_allnations')->__('Exception: Um erro inesperado ocorreu. Contate o suporte'));
            $this->_redirect('*/*/');
        }
    }
}