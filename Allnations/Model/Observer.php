<?php


class Novapc_AllNations_Model_Observer extends Varien_Event_Observer
{

    public function checkOrder(Varien_Event_Observer $event)
    {
        $fatDir = Mage::getStoreConfig('allnations/allnationsorders/usarfatdir', Mage::app()->getStore());

        if ($fatDir == 0) {
            # Carrega a order e o model pra interação com o banco
            $order = $event->getEvent()->getOrder();

            # Verifica qual o atributo que representa o ID da All Nations
            $control = Mage::getStoreConfig('allnations/general/id_allnations', Mage::app()->getStore());

            $productCollection = Mage::getModel('catalog/product');

            # Pega todos os IDs dos produtos no pedido e salva numa array
            foreach ($order->getAllItems() as $item) {
                $product = $productCollection->load($item->getProductId());

                if ($product->getAllnationsSync() == 1) {
                    $orderId = $order->getId();
                    $productId = $product->getData($control);
                    $qty = $item->getQtyOrdered();

                    Mage::helper('novapc_allnations')
                        ->insertOrder($orderId, $productId, $qty);

                    $generate = 1;
                }
            }

            if ($generate == 1) {
                $model = Mage::getModel('novapc_allnations/order');
                $model->setOrder($order->getId());
                $model->setRealOrderId($order->getIncrementId());
                $model->setCustomer($order->getCustomerName());
                $model->setCreatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:m:s'));
                $model->save();
            }
        }

        return;
    }

    public function confirmReserve(Varien_Event_Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            $ANorders = Mage::getModel('novapc_allnations/order');
            $processing = $order::STATE_PROCESSING;
            $canceled = $order::STATE_CANCELED;

            if ($order->getStatus() == $processing) {
                $id = $order->getId();
                $filter = $ANorders->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('order', ['eq' => $id]);

                if ($filter->getSize() > 0) {
                    Mage::helper('novapc_allnations')->confirmOrder($id);
                }
            } elseif ($order->getStatus() == $canceled) {
                $id = $order->getId();
                $filter = $ANorders->getCollection()
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('order', ['eq' => $id]);

                if ($filter->getSize() > 0) {
                    Mage::helper('novapc_allnations')->cancelOrder($id);
                }
            }
        } catch (Exception $e) {

        }
    }

    public function updatePromotions(Varien_Event_Observer $observer)
    {
        if ($observer->getBlock()->getType() == 'novapc_allnations/adminhtml_promotion') {
            self::deletePromotions();

            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx' .
                '/RetornarListaProdutosPromocao?' .
                'CodigoCliente=' . $apiUser . '&Senha=' . $apiPassword;

            $return = Mage::helper('novapc_allnations')->callCurl('GET', $url);

            # Se o http code for 200 (sucesso), fazer:
            if (in_array('200', $return['httpCode'])) {
                $ANpromos = $return['response']['diffgrdiffgram']['NewDataSet']['Produtos'];

                $collection = Mage::getModel('novapc_allnations/promotion');
                $promo = Mage::getModel('novapc_allnations/promotion');

                foreach ($ANpromos as $product) {
                    # Filtra a collection atual de promoções, se o ID do produto atual ja estiver la, não adiciona
                    $filter = $collection->getCollection()->addFieldToFilter('id_product', $product['CODIGO']);
                    if ($filter->getSize() == 0) {
                        $promo->setData(array(
                            'name'             => $product['DESCRICAO'],
                            'id_product'       => $product['CODIGO'],
                            'promo_price'      => $product['PRECOREVENDA'],
                            'descrtec'         => $product['DESCRTEC'],
                            'category'         => $product['CATEGORIA'],
                            'sub_category'     => $product['SUBCATEGORIA'],
                            'manufacturer'     => $product['FABRICANTE'],
                            'department'       => $product['DEPARTAMENTO'],
                            'partnumber'       => $product['PARTNUMBER'],
                            'ean'              => $product['EAN'],
                            'warranty'         => $product['GARANTIA'],
                            'weight'           => $product['PESOKG'],
                            'resale_price'     => $product['PRECOREVENDA'],
                            'price_without_st' => $product['PRECOSEMST'],
                            'expire_date'      => $product['DATAVALIDADEPRECO'],
                            'available'        => $product['DISPONIVEL'],
                            'pic'              => $product['URLFOTOPRODUTO'],
                            'stock'            => $product['ESTOQUE'],
                            'ncm'              => $product['NCM'],
                            'width'            => $product['LARGURA'],
                            'height'           => $product['ALTURA'],
                            'depth'            => $product['PROFUNDIDADE'],
                            'active'           => $product['ATIVO'],
                            'subst_tributaria' => $product['SUBSTTRIBUTARIA'],
                            'product_origin'   => $product['ORIGEMPRODUTO'],
                            'available_stock'  => $product['ESTOQUEDISPONIVEL'],
                            'updated_at'       => $product['TIMESTAMP']
                        ));
                        $promo->save();
                    }
                }
            }
        }
    }

    public function deletePromotions()
    {
        # Data e hora atual
        $today = Mage::getModel('core/date')->gmtDate('Y-m-d H:m:s');

        # Pega somente os produtos que o campo 'expire_date' seja anterior a data e hora atual
        $promo = Mage::getResourceModel('novapc_allnations/promotion_collection')
            ->addFieldToFilter('expire_date', array('lt' => $today));

        $products = Mage::getModel('catalog/product');

        # Verifica qual o atributo que representa o ID da All Nations
        $control = Mage::getStoreConfig('allnations/general/id_allnations', Mage::app()->getStore());

        # Pra cada promoção expirada, deletar ela
        foreach ($promo as $single) {
            # Pega o produto, deleta da tabela de promoções e muda o atributo de controle
            $product = $products->loadByAttribute($control, $single->getIdProduct());

            $product->setAllnationsPromo(0);
            $product->save();

            $single->delete();
        }
    }

    public function setAttributes()
    {
        $attr = Mage::getModel('novapc_allnations/attr');
        $attr->setData(array(
            'fabricante'        => Mage::getStoreConfig('allnations/attributes/fabricante', Mage::app()->getStore()),
            'part_number'       => Mage::getStoreConfig('allnations/attributes/part_number', Mage::app()->getStore()),
            'ean'               => Mage::getStoreConfig('allnations/attributes/ean', Mage::app()->getStore()),
            'garantia'           => Mage::getStoreConfig('allnations/attributes/garantia', Mage::app()->getStore()),
            'peso'              => Mage::getStoreConfig('allnations/attributes/peso', Mage::app()->getStore()),
            'preco_sem_st'      => Mage::getStoreConfig('allnations/attributes/preco_sem_st', Mage::app()->getStore()),
            'ncm'               => Mage::getStoreConfig('allnations/attributes/ncm', Mage::app()->getStore()),
            'largura'           => Mage::getStoreConfig('allnations/attributes/largura', Mage::app()->getStore()),
            'altura'            => Mage::getStoreConfig('allnations/attributes/altura', Mage::app()->getStore()),
            'profundidade'      => Mage::getStoreConfig('allnations/attributes/profundidade', Mage::app()->getStore()),
            'subst_tributaria'  => Mage::getStoreConfig('allnations/attributes/subst_tributaria', Mage::app()->getStore()),
            'origem_produto'    => Mage::getStoreConfig('allnations/attributes/origem_produto', Mage::app()->getStore()),
        ));
        $attr->setId('1');
        $attr->save();
    }

    public function stockUpdate(Varien_Event_Observer $observer)
    {
        try {
//            # Produto
//            $event = $observer->getEvent()->getItem()->getProduct();
//
//            if ($event->getAllnationsSync() == 1) {
//
//                # Verifica qual o atributo que representa o ID da All Nations
//                $control = Mage::getStoreConfig('allnations/general/id_allnations', Mage::app()->getStore());
//                $id = $event->getData($control);
//                Mage::helper('novapc_allnations')->updateStock($id);
//            }
        } catch (Exception $e) {

        }
    }

    public function updateOrderStatus(Varien_Event_Observer $observer)
    {
        if ($observer->getBlock()->getType() == 'novapc_allnations/adminhtml_order') {

            $ANorders = Mage::getModel('novapc_allnations/order')->getCollection();
            $helper = Mage::helper('novapc_allnations');

            foreach ($ANorders as $single) {
                $order = $helper->returnOrders($single->getOrder());

                if ($order['STATUS'] == '') {
                    $single->setStatus($order[0]['STATUS']);
                } else {
                    $single->setStatus($order['STATUS']);
                }

                $single->save();
            }
        }
    }

    public function orderUpdate()
    {

    }
}