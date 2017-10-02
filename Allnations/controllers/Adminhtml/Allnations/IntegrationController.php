<?php

/**
 * Atualizar admin controller
 *
 * @category    Novapc
 * @package     Novapc_Allnations
 * @author      .
 */
class Novapc_Allnations_Adminhtml_Allnations_IntegrationController extends Mage_Adminhtml_Controller_Action
{

    protected function _initIntegration()
    {
        $integrationId  = (int) $this->getRequest()->getParam('id');
        $integration    = Mage::getModel('novapc_allnations/integration');
        if ($integrationId) {
            $integration->load($integrationId);
        }
        Mage::register('current_integration', $integration);
        return $integration;
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
            ->_title(Mage::helper('novapc_allnations')->__('Integration'));
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



    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('novapc_allnations/integration');
    }

    public function massCategoryAction()
    {
        try {
            # Pega os IDs selecionados pela MassAction
            $integrationFilter = $this->getRequest()->getParams();

                # Se a opção escolhida for a de categorias, fazer:
            if ($integrationFilter['allnations_integration'][0] == 1) {

                # Força o load da página em modo ADMIN
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

                # Pega usuario e senha colocados na config
                $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
                $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

                # hoje
                $today = date("Y-m-d");

                # Carrega o model de integração, pega a data da ultima atualização
                $integration = Mage::getModel('novapc_allnations/integration')
                    ->load(1);
                $updatedAt = $integration->getUpdatedAt();

                # Se existir, usar a data da ultima atualização, se não, usar a data de hoje
                if ($updatedAt) {
                    $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('Y-m-d');
                    $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/RetornarListaProdutosEstoque' .
                        '?CodigoCliente=' . $apiUser . '&Senha=' . $apiPassword . '&Data=' . $updatedAt;
                } else {
                    $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/RetornarListaProdutosEstoque' .
                        '?CodigoCliente=' . $apiUser . '&Senha=' . $apiPassword . '&Data=' . $today;
                }

                # Chama o curl
                $return = Mage::helper('novapc_allnations')->callCurl('GET', $url);

                # Verifica se o httpCode é 200 (OK)
                if (in_array('200', $return['httpCode'])) {

                    # Cria uma array somente com os produtos
                    $ANproducts = $return['response']['diffgrdiffgram']['NewDataSet']['Produtos'];

                    # Instância o model de categoria, pega o id selecionado na config e da load no mesmo
                    $category = Mage::getModel('catalog/category');
                    $id = Mage::getStoreConfig('allnations/general/category_allnations', Mage::app()->getStore());
                    $parentCategory = Mage::getModel('catalog/category')
                        ->load($id);

                    # Pra cada resposta da allnations, fazer:
                    foreach ($ANproducts as $single) {

                        # Filtra a categoria e a subcategoria pra verificar se ja existem
                        $filter = $category->getCollection()
                            ->addAttributeToFilter('name', ['eq' => $single['CATEGORIA']])
                            ->addAttributeToSelect('*');

                        $subFilter = $category->getCollection()
                            ->addAttributeToFilter('name', ['eq' => $single['SUBCATEGORIA']])
                            ->addAttributeToSelect('*');

                        # Se ambas não existirem, fazer:
                        if ($filter->getSize() == 0 and $subFilter->getSize() == 0) {
                            $category->setName($single['CATEGORIA']);
                            $category->setUrlKey($single['CATEGORIA']);
                            $category->setIsActive(1);
                            $category->setDisplayMode('PRODUCTS');
                            $category->setIsAnchor(0); //for active anchor
                            $category->setStoreId(Mage::app()->getStore()->getId());

                            $category->setPath($parentCategory->getPath());
                            $category->save();
                            $category->unsetData();

                            # Se a subcategoria não tiver o mesmo nome que a categoria
                            if ($single['CATEGORIA'] != $single['SUBCATEGORIA']) {
                                $category->setName($single['SUBCATEGORIA']);
                                $category->setUrlKey($single['SUBCATEGORIA']);
                                $category->setIsActive(1);
                                $category->setDisplayMode('PRODUCTS');
                                $parent = $category->loadByAttribute('name', $single['CATEGORIA']);
                                $category->setIsAnchor(0); //for active anchor
                                $category->setStoreId(Mage::app()->getStore()->getId());
                                $category->setPath($parent->getPath());
                                $category->save();
                                $category->unsetData();
                            }

                            # Se existir a categoria, mas não a subcategoria, fazer:
                        } elseif ($filter->getSize() != 0 and $subFilter->getSize() == 0) {

                            # Se a subcategoria não tiver o mesmo nome que a categoria
                            if ($single['CATEGORIA'] != $single['SUBCATEGORIA']) {
                                $category->setName($single['SUBCATEGORIA']);
                                $category->setUrlKey($single['SUBCATEGORIA']);
                                $category->setIsActive(1);
                                $category->setDisplayMode('PRODUCTS');
                                $parent = $category->loadByAttribute('name', $single['CATEGORIA']);
                                $category->setIsAnchor(0); //for active anchor
                                $category->setStoreId(Mage::app()->getStore()->getId());
                                $category->setPath($parent->getPath());
                                $category->save();
                                $category->unsetData();
                            }
                        }
                    }


                    # Pega o valor da primeira integração de categorias
                    $firstUpdate = $integration->getData('first_update');

                    # Se ele for vazio, fazer:
                    if (!$firstUpdate) {
                        $integration->setFirstUpdate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->setUpdatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->save();
                        # Se não for vazio, fazer:
                    } else {
                        $integration->setUpdatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->save();
                    }

                    # Sucesso
                    Mage::getSingleton('core/session')
                        ->addSuccess(Mage::helper('novapc_allnations')->__('Integração de categorias completa.'));
                    $this->_redirect('*/*/');
                    # Se o httpCode for 500, mostra a mensagem de erro
                } elseif (in_array('500', $return['httpCode'])) {
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
                ->addError(Mage::helper('novapc_allnations')->__('Um erro inesperado ocorreu. Contate o suporte'));
            $this->_redirect('*/*/');
        }

    }

    public function massIntegrateProductAction()
    {
        try{
            # Pega os IDs selecionados pela MassAction
            $filter = $this->getRequest()->getParams();

            if($filter['allnations_integration'][0] == 2) {

                # Força o load da página em modo ADMIN
                Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

                # Pega usuario e senha colocados na config
                $apiUser = Mage::getStoreConfig('allnations/general/api_user', Mage::app()->getStore());
                $apiPassword = Mage::getStoreConfig('allnations/general/api_password', Mage::app()->getStore());

                # hoje
                $today = date("Y-m-d");
                $integration = Mage::getModel('novapc_allnations/integration')
                    ->load(2);

                $updatedAt = $integration->getUpdatedAt();
                if ($updatedAt) {
                    $updatedAt = DateTime::createFromFormat('Y-m-d H:i:s', $updatedAt)->format('Y-m-d');
                    $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/RetornarListaProdutosEstoque' .
                        '?CodigoCliente=' . $apiUser . '&Senha=' . $apiPassword . '&Data=' . $updatedAt;
                } else {
                    $url = 'http://wspub.allnations.com.br/wsIntEstoqueClientesV2/ServicoReservasPedidosExt.asmx/RetornarListaProdutosEstoque' .
                        '?CodigoCliente=' . $apiUser . '&Senha=' . $apiPassword . '&Data=' . $today;
                }

                $return = Mage::helper('novapc_allnations')->callCurl('GET', $url);

                # Verifica se o httpCode é 200 (OK)
                if (in_array('200', $return['httpCode'])) {

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

                    # Carrega os models de produtos e categorias
                    $products = Mage::getModel('catalog/product');
                    $category = Mage::getModel('catalog/category');

                    $ANproducts = $return['response']['diffgrdiffgram']['NewDataSet']['Produtos'];

                    $integration = Mage::getModel('novapc_allnations/integration')
                        ->load(2);

                    $loop = 0;
                    foreach ($ANproducts as $single) {
                        # Filtra pra ver se esse produto ja existe no catalogo e se esta sincronizado
                        $filter = $products->getCollection()
                            ->addAttributeToFilter($control, ['eq' => $single['CODIGO']])
                            ->addAttributeToFilter('allnations_sync', ['eq' => 1]);

                        # Se não estiver:
                        if ($filter->getSize() == 0) {
                            $products->setData(array(
                                    'entity_type_id'    => '4',
                                    'attribute_set_id'  => $products->getDefaultAttributeSetId(),
                                    $control            => $single['CODIGO'],
                                    $attr['0']          => $single['FABRICANTE'],
                                    $attr['1']          => $single['PARTNUMBER'],
                                    $attr['2']          => $single['EAN'],
                                    $attr['3']          => $single['GARANTIA'],
                                    $attr['4']          => $single['PESOKG'],
                                    $attr['5']          => $single['PRECOSEMST'],
                                    $attr['6']          => $single['NCM'],
                                    $attr['7']          => $single['LARGURA'],
                                    $attr['8']          => $single['ALTURA'],
                                    $attr['9']         => $single['PROFUNDIDADE'],
                                    $attr['10']         => $single['SUBSTTRIBUTARIA'],
                                    $attr['11']         => $single['ORIGEMPRODUTO']
                                )
                            );

                            $products->setName($single['DESCRICAO'])
                                ->setDescription($single['DESCRTEC'])
                                ->setShortDescription($single['DESCRICAO'])
                                ->setAvailable($single['DISPONIVEL'])
                                ->setStatus($single['ATIVO'])
                                ->setSku($single['CODIGO'])
                                ->setWebsiteIds(array(1))
                                ->setTypeId('simple')
                                ->setCreatedAt(strtotime('now'))
                                ->setTaxClassId(4)
                                ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                                ->setPrice($single['PRECOREVENDA'])
                                ->setWeight($single['PESOKG'])
                                ->setStoreId(Mage::app()->getStore()->getId());


                            # Pega a CATEGORIA e a SUBCATEGORIA
                            $mainCat = $single['CATEGORIA'];
                            $subCat = $single['SUBCATEGORIA'];

                            # Faz uma checagem para ver se a categoria e a sub-categoria são iguais,
                            # se forem, adiciona somente na principal, se não forem, adiciona nas duas
                            if ($mainCat == $subCat) {
                                $filter = $category->getCollection()
                                    ->addAttributeToFilter('name', ['eq' => $mainCat])
                                    ->addAttributeToSelect('*');

                                if ($filter->getSize() > 0) {
                                    $filter = $filter->getFirstItem();

                                    $products->setCategoryIds($filter->getId());
                                } else {
                                    $id = Mage::getStoreConfig('allnations/general/category_allnations', Mage::app()->getStore());
                                    $products->setCategoryIds($id);
                                }
                            } else {
                                $filter = $category->getCollection()
                                    ->addAttributeToFilter('name', ['eq' => $mainCat])
                                    ->addAttributeToSelect('*');

                                $subFilter = $category->getCollection()
                                    ->addAttributeToFilter('name', ['eq' => $subCat])
                                    ->addAttributeToSelect('*');

                                if ($filter->getSize() > 0 and $subFilter->getSize() > 0) {
                                    $filter = $filter->getFirstItem()
                                        ->getId();
                                    $subFilter = $subFilter->getFirstItem()->getId();

                                    $products->setCategoryIds(array($filter, $subFilter));
                                }  elseif ($filter->getSize() > 0 and $subFilter->getSize() == 0) {

                                    $filter = $filter->getFirstItem();
                                    $products->setCategoryIds($filter->getId());

                                } elseif ($filter->getSize() == 0 and $subFilter->getSize() > 0) {

                                    $subFilter = $subFilter->getFirstItem();
                                    $products->setCategoryIds($subFilter->getId());

                                } else {

                                    $id = Mage::getStoreConfig('allnations/general/category_allnations', Mage::app()->getStore());
                                    $products->setCategoryIds($id);

                                }
                            }



                            $filepath   = Mage::getBaseDir('media') . DS . 'import'. DS . $single['CODIGO'] ; //path for temp
                            // storage
                            $image_url = $single['URLFOTOPRODUTO'];
                            $image_url = str_replace('http//', 'http://', $image_url);
                            $ch = curl_init($image_url);

                            $fp = fopen($filepath . '.png', 'wb');
                            curl_setopt($ch, CURLOPT_FILE, $fp);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_exec($ch);
                            curl_close($ch);
                            fclose($fp);

                            $mediaAttribute = array (
                                'thumbnail',
                                'small_image',
                                'image'
                            );

                            $products->addImageToMediaGallery($filepath . '.png', $mediaAttribute, false, false);

                            if ($single['ESTOQUEDISPONIVEL'] > 0) {
                                $products->setStockData([
                                    'qty' => $single['ESTOQUEDISPONIVEL'],
                                    'is_in_stock' => 1,
                                    'use_config_max_sale_qty' => 0,
                                    'max_sale_qty' => '5'
                                ]);
                            } else {
                                $products->setStockData([
                                    'qty' => $single['ESTOQUEDISPONIVEL'],
                                    'is_in_stock' => 0,
                                    'use_config_max_sale_qty' => 0,
                                    'max_sale_qty' => '5'
                                ]);
                            }
                            $products->setAllnationsSync(1);
                            $products->save();

                            $loop++;
                            if ($loop == 10) {
                                break;
                            }
                        }
                    }


                    $firstUpdate = $integration->getData('first_update');

                    if (!$firstUpdate) {
                        $integration->setFirstUpdate(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->setUpdatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->save();
                    } else {
                        $integration->setUpdatedAt(Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s'));
                        $integration->save();
                    }


                    Mage::getSingleton('core/session')
                        ->addSuccess(Mage::helper('novapc_allnations')->__('Integração de produtos completa.'));
                    $this->_redirect('*/*/');
                } elseif (in_array('500', $return['httpCode'])) {
                    Mage::getSingleton('core/session')
                        ->addError(Mage::helper('novapc_allnations')->__('Http Code: 500. Usuario e/ou senha incorretos. Caso o erro persista, contate o suporte'));
                    $this->_redirect('*/*/');
                } else {
                    Mage::getSingleton('core/session')
                        ->addError(Mage::helper('novapc_allnations')->__('Um erro inesperado ocorreu. Contate o suporte'));
                    $this->_redirect('*/*/');
                }

            }
        } catch(Exception $e) {
            Mage::getSingleton('core/session')
                ->addError(Mage::helper('novapc_allnations')->__('Um erro inesperado ocorreu. Contate o suporte'));
            $this->_redirect('*/*/');
        }
    }

    public function massUpdateStockAction()
    {
        try {
            # Pega os IDs selecionados pela MassAction
            $filter = $this->getRequest()->getParams();

            if ($filter['allnations_integration'][0] == 3) {
                $update = Mage::helper('novapc_allnations')->updateStock();
                if ($update == 200) {
                    Mage::getSingleton('core/session')
                        ->addSuccess(Mage::helper('novapc_allnations')->__('Atualização completa.'));
                    $this->_redirect('*/*/');
                } elseif ($update == 500) {
                    Mage::getSingleton('core/session')
                        ->addError(Mage::helper('novapc_allnations')->__('Http Code: 500. Usuario e/ou senha incorretos. Caso o erro persista, contate o suporte'));
                    $this->_redirect('*/*/');
                } else {
                    Mage::getSingleton('core/session')
                        ->addError(Mage::helper('novapc_allnations')->__('Um erro inesperado ocorreu. Contate o suporte'));
                    $this->_redirect('*/*/');
                }
            }
        } catch(Exception $e) {
            Mage::getSingleton('core/session')
                ->addError(Mage::helper('novapc_allnations')->__('Um erro inesperado ocorreu. Contate o suporte'));
            $this->_redirect('*/*/');
        }
    }
}