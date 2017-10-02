<?php

/**
 * Allnations default helper
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author .
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    public static function callCurl($method, $url, $body = null)
    {
        $headers = array(
            "Content-type: application/x-www-form-urlencoded",
            "Accept: application/x-www-form-urlencoded"
        );

        $connection = new Varien_Http_Adapter_Curl();

        if ($method == "GET") {
            $zendMethod = Zend_Http_Client::GET;
        } elseif ($method == "POST") {
            $zendMethod = Zend_Http_Client::POST;
        } elseif ($method == "PUT") {
            $zendMethod = Zend_Http_Client::PUT;
            //ADICIONA AS OPTIONS MANUALMENTE POIS NATIVAMENTE O WRITE NAO VERIFICA POR PUT
            $connection->addOption(CURLOPT_CUSTOMREQUEST, "PUT");
            $connection->addOption(CURLOPT_POSTFIELDS, $body);
        }


        $connection->setConfig(
            array(
                'timeout'   => 30
            )
        );

        #Grava, lê e fecha a conexão
        $connection->write($zendMethod, $url, '1.0', $headers, $body);
        $response = $connection->read();
        $connection->close();

        #Extrai o httpcode e o BODY de resposta
        $httpCode = Zend_Http_Response::extractCode($response);
        $response = Zend_Http_Response::extractBody($response);

        #Guarda a resposta em uma variavel diferente e depois decodifica a primeira
        $arrResp  = $response;
        $response = json_decode($response, true);

        $response['httpCode'] = $httpCode;

        #Transforma a resposta guardada anteriormente em DOMDocument, depois em XML
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;
        $doc->loadXML($arrResp);
        $doc->save('t.xml');

        #Pega o xml e transforma em Array
        $xmlfile  = file_get_contents('t.xml');
        $parseObj = str_replace($doc->lastChild->prefix.':',"",$xmlfile);
        $ob       = simplexml_load_string($parseObj);
        $data     = json_decode(json_encode($ob), true);


        $response = [
            'httpCode' => $response,
            'response' => $data
        ];

        return $response;
    }

    public function updateStock($codigo = null)
    {
        try {
            # Força o load da página em modo ADMIN
            Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            # Model da integração, para pegar a data da ultima atualização
            $integration = Mage::getModel('novapc_allnations/integration');

            $productIntegration = $integration->load(2);
            $integration = $integration->load(3);

            # Checa qual é maior, se é a data da integração dos produtos ou da atualização
            if ($integration->getUpdatedAt() < $productIntegration->getUpdatedAt()) {
                $updatedAt = substr($integration->getUpdatedAt(), 0, 9);
            } else {
                $updatedAt = substr($productIntegration->getUpdatedAt(), 0, 9);
            }

            # Verifica qual o atributo que representa o ID da All Nations
            $control = Mage::getStoreConfig('allnations/general/id_allnations', Mage::app()->getStore());

            # Instancia o model de estoque
            $stockItem = Mage::getModel('cataloginventory/stock_item');

            # Variavel de controle
            $success = 0;

            # Se for chamado sem o parametro $codigo, atualiza todos os produtos
            if ($codigo == null) {
                $ANProducts = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('allnations_sync', ['eq' => 1]);

                foreach ($ANProducts as $single) {
                    $id = $single->getData($control); // Pega o ID da All Nations
                    $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/ConsultaEstoqueProdutos' .
                        '?CodigoCliente=' . $apiUser .
                        '&Senha=' . $apiPassword .
                        '&CodigoProduto=' . $id .
                        '&Data=' . $updatedAt;

                    $return = Mage::helper('novapc_allnations')->callCurl('GET', $url);
                    $httpCode = $return['httpCode']['httpCode'];

                    $return = $return['response']['diffgrdiffgram']['NewDataSet']['Estoques'];

                    if ($httpCode == '200') {

                        if ($return['CODIGO'] == $single->getData($control)) {

                            if ($single->getData('allnations_promo')     == 0) {
                                $single->setPrice
                                ($return['PRECOREVENDA']);
                            } else {
                                $single->setSpecialPrice
                                ($return['PRECOREVENDA']);
                            }

                            $single->save();

                            $stock = $stockItem->loadByProduct($single->getId());
                            $stock->setQty($return['ESTOQUEDISPONIVEL']);
                            $stock->setIsInStock($return['DISPONIVEL']);

                            $stock->save();

                            if ($success != 1) {
                                $success = 1;
                            };
                        }
                    } else {
                        return $httpCode;
                    }
                }

                if ($success == 1) {
                    $firstUpdate = $integration->getData('first_update');

                    # Checa se ja foi atualizado antes
                    if (!$firstUpdate) {
                        $integration->setFirstUpdate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->setUpdatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->save();
                    } else {
                        $integration->setUpdatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->save();
                    }

                    return $httpCode;
                }
            } else {

                $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/ConsultaEstoqueProdutos' .
                    '?CodigoCliente=' . $apiUser .
                    '&Senha=' . $apiPassword .
                    '&CodigoProduto=' . $codigo .
                    '&Data=' . $updatedAt;

                $return = Mage::helper('novapc_allnations')->callCurl('GET', $url);
                $httpCode = $return['httpCode']['httpCode'];

                if ($httpCode == '200') {

                    $product = Mage::getModel('catalog/product')
                        ->loadByAttribute($control, $codigo);


                    if ($return['CODIGO'] == $product->getData($control)) {

                        if ($product->getData('allnations_promo') == 0) {
                            $product->setPrice
                            ($return['PRECOREVENDA']);
                        } else {
                            $product->setSpecialPrice
                            ($return['PRECOREVENDA']);
                        }

                        $product->save();

                        $stock = $stockItem->loadByProduct($product->getId());
                        $stock->setQty($return['ESTOQUEDISPONIVEL']);
                        $stock->setIsInStock($return['DISPONIVEL']);

                        $stock->save();

                        return $httpCode;
                    }
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function insertOrder($order, $product, $qty)
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/InserirReserva?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&PedidoCliente=' . $order .
                '&CodigoProduto=' . $product .
                '&Qtd=' . $qty;

            $curl = self::callCurl('GET', $url);

            $httpCode = $curl['httpCode'];

            return $httpCode;
        } catch (Exception $e){
            return $e->getMessage();
        }
    }

    public static function insertDirectOrder($order, $product, $qty, $cnpjCpf, $value, $adress)
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/InserirReservaFatDir?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&PedidoCliente=' . $order .
                '&CodigoProduto=' . $product .
                '&Qtd=' . $qty .
                '&CNPJ_CPF=' . $cnpjCpf .
                '&ValorVenda=' . $value .
                '&Endereco=' . $adress;

            $curl = self::callCurl('GET', $url);

            $httpCode = $curl['httpCode'];

            return $httpCode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function returnOrders($order = '')
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/RetornarReservas?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&PedidoCliente=' . $order;

            $curl = Mage::helper('novapc_allnations')->callCurl('GET', $url);

            $httpCode = $curl['response']['diffgrdiffgram']['NewDataSet']['Reservas'];
            return $httpCode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function registerCustomer($customer)
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/CadastrarCliente?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&DadosCliente=' . $customer;

            $curl = Mage::helper('novapc_allnations')->callCurl('GET', $url);
            $httpCode = $curl['httpCode'];

            return $httpCode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function registerAdress($adress)
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/CadastrarEndereco?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&DadosEndereco=' . $adress;

            $curl = Mage::helper('novapc_allnations')->callCurl('GET', $url);
            $httpCode = $curl['httpCode'];

            return $httpCode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function confirmOrder($order)
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/ConfirmarReserva?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&PedidoCliente=' . $order;

            $curl = Mage::helper('novapc_allnations')->callCurl('GET', $url);
            $httpCode = $curl['httpCode'];

            return $httpCode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function confirmDirectOrder($order, $adress)
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/ConfirmarReservaFatDir?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&PedidoCliente=' . $order .
                '&Endereco=' . $adress;

            $curl = Mage::helper('novapc_allnations')->callCurl('GET', $url);
            $httpCode = $curl['httpCode'];

            return $httpCode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function cancelOrder($order)
    {
        try {
            # Pega usuario e senha colocados na config
            $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
            $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

            $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/CancelarReserva?' .
                'CodigoCliente=' . $apiUser .
                '&Senha=' . $apiPassword .
                '&PedidoCliente=' . $order;

            $curl = Mage::helper('novapc_allnations')->callCurl('GET', $url);
            $httpCode = $curl['httpCode'];

            return $httpCode;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}