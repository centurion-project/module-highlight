<?php

/**
 * The generic controller for highlights
 */
abstract class Highlight_Model_Controller_AdminHighlightController extends Centurion_Controller_Action
{
    /**
     * The current container we are managing
     */
    protected $_container = null;

    /**
     * The select object we use for a list of containers
     */
    protected $_select = null;
    
    public function preDispatch()
    {
        $view   = $this->initView();
        // make sure our views always get found
        $view->addBasePath($this->getFrontController()->getModuleDirectory('highlight') . DIRECTORY_SEPARATOR . 'views');
        return parent::preDispatch();
    }
    
    /** 
     * @deprecated
     */
    public function actions($row)
    {
        return '<a href="'.$this->view->url(array('id' => $row->id, 'module' => 'highlight', 'controller' => 'admin-highlight'), 'rest', true).'">'.$this->view->translate('Manage highlight').'</a>';
    }
    
    
    /**
     * implements callback url logic. it looks for a `returnto` parameter and sets any relevant redirection
     * in the response
     */
    protected function _returnTo()
    {
        if (null !== $this->_getParam('returnto', null))
            $this->_response->setRedirect(urldecode($this->_getParam('returnto')));
        else
            $this->_response->setRedirect($this->view->url(array('action' => 'get')));
    }
    
    /**
     * Lists all containers by their names
     * if a proxy exists, only lists named containers attached to this proxy
     */
    public function indexAction()
    {
        $this->view->containers = $this->getContainers();
        $this->view->proxy = $this->_getProxy();

        $this->render('admin-highlight/index', true, true);
    }

    /**
     * returns a colection of containers to list
     */
    public function getContainers()
    {
        $select = $this->getSelect();
        return $select->fetchAll();
    }

    /**
     * returns a select object to retrieve the containers from base
     * @return Centurion_Db_Table_Select 
     */
    public function getSelect()
    {
        if($this->_select) return $this->_select;

        $this->_select = Centurion_Db::getSingleton('highlight/container')->select(true);
        // if there's a proxy, only list containers with this proxy
        if($proxy = $this->_getProxy()) {
            $this->_select->filter(array(
                'proxy_pk'                  => $proxy->pk,
                'proxy_content_type__name'  => get_class($proxy->getTable())
            ));
        }
        // else, only list containers with no proxy from config
        else {
            $this->_select->filter(array(
                'proxy_pk__isnull'  => true
            ));
            $named = $this->_getNamedHighlights();
            if(!empty($named)) {
                $this->_select->filter(array(
                    'name__in'      => $named
                ));
            }
        }
        return $this->_select;
    }

    /**
     * Proxy to the bootstrap static function
     */
    protected function _getNamedHighlights()
    {
        return Highlight_Bootstrap::listNamedHighlightsInConfig();
    }
    
    /** 
     * @tries to retrieve a container from the given uri parameters
     * it searches in the following parameters
     *
     * a `container` parameter,  manually set and is an instance of a container
     * the `id` parameter. it will retrieve it from base from that id
     *`proxy_px` and `proxy_content_type_id` parameters, it will find a container linked to the described proxy model
     * @parameter (boolean) $redirect404 redirect the request to a 404 error if no container is found
     */
    protected function _getContainer($redirect404 = true)
    {
        if (!isset($this->_container)) {
            if (($container = $this->_getParam('container', null)) && ($container instanceof Highlight_Model_DbTable_Row_Container)) {
                $this->_container = $container;
            } else if ($id = $this->_getParam('id', null)) {
                $this->_container = $this->_helper->getObjectOr404('highlight/container', array('id' =>  $this->_getParam('id')));
            } else if ($this->_hasProxyParam()) {
                $proxy = $this->_getProxy();
                $container = Centurion_Db::getSingleton('highlight/container')->findOneByProxy($proxy);
                if(!is_null($container)) {
                    $this->_container = $container;
                }
                else {
                    $this->_container = Centurion_Db::getSingleton('highlight/container')->createWithProxy($proxy);
                }
            } else {
                throw new Zend_Controller_Action_Exception(sprintf('No container given'), 404);
            }
        }
        return $this->_container;
    }

    /**
     * retrieve a content from base with the given query parameters
     * it looks for the `proxy_pk` and `proxy_content_type_id` parameters to establish what content and content type
     * it needs
     * @return Centurion_Db_Table_Row_Abstract
     */
    protected function _getProxy()
    {
        if(!$this->_hasProxyParam()) {
            return null;
        }
        $ctype = Centurion_Db::getSingleton('core/content_type')->findOneById($this->_getParam('proxy_content_type_id'));
        $model = Centurion_Db::getSingletonByClassName($ctype->name);
        $row = $model->findOneById($this->_getParam('proxy_pk'));
        return $row;
    }

    /**
     * detects if the necessary parameters to find a proxy object are present in the request
     * looks for `proxy_pk` and `proxy_content_type_id`
     * @return boolean
     */
    protected function _hasProxyParam()
    {
        return ($this->_getParam('proxy_pk', false) && $this->_getParam('proxy_content_type_id'));
    }
    
    /**
     * Gets called from xhr by the autocomplete plugin.
     * returns a json view of a list of models that matches the query
     */
    public function autocompleteAction()
    {
        $container = $this->_getContainer();
        
        $crawler = $this->getCrawler();
        $resultArray = $crawler->autocomplete($this->_getParam('term'));

        $response = $this->getResponse();
        
        $this->_helper->Json($resultArray);
        
    }

    /**
     * returns the crawler to be used upon autocomplete action
     * @return Highlight_Model_Crawler_Abstract
     */
    public function getCrawler()
    {
        // maybe the proxy can tell us what crawler we need to use
        $proxy = $this->_getProxy();
        if(!empty($proxy) && $proxy instanceof Highlight_Traits_Model_Row_HasHighlights_Interface) {
            $crawler = $proxy->getCrawler();
        }
        
        // maybe there was a crawler parameter in the url
        // we can try and find it from config.
        if($this->_getParam('crawler', false)) {
            $crawlerName = $this->_getParam('crawler');
            $crawler = Highlight_Model_Crawler_Factory::get($crawlerName);
        }

        // maybe the container is a named container and has a crawler in its config
        if($container = $this->_getContainer()) {
            if(!empty($container->name)) {
                $name = $container->name;
                $crawlerName = Centurion_Config_Manager::get(sprintf('highlight.named_highlights.%s.crawler', $name));
                if($crawlerName) {
                    $crawler = Highlight_Model_Crawler_Factory::get($crawlerName);
                }
            }
        }
        
        // if everything else fails, get default crawler
        if(empty($crawler)) {
            $crawler = Highlight_Model_Crawler_Factory::get('default');
        }

        // some crawlers may need the container we are talking about
        $crawler->setContainer($this->_getContainer());
        return $crawler;
    }

    /**
     * returns a form object for the autocomplete part
     */
    public function _getAutocompleteForm()
    {
        $form = new Highlight_Form_Model_Item();
        $form->setContainer($this->_getContainer());
        $form->cleanForm();
        $form->setMethod(Centurion_Form::METHOD_POST);
        return $form;
    }
    
    public function getAction()
    {
        $this->view->container = $this->_getContainer();
        if($backUrl = $this->_getParam('returnafter', false)) {
            $this->view->backUrl = urldecode($backUrl);
        }

        $form = $this->_getAutocompleteForm();
        $form->setAction($this->view->url(array(
            'action'=>'add',
            'id'=>$this->view->container->id,
            'returnto'=> $this->view->url()
        )));
        $this->view->autoCompleteForm = $form;

        $this->view->highlightMapper = $this->getMapper();

        $this->render('admin-highlight/get', true, true);
    }

    /**
     * returns the mapper to be used upon listing of the highlight
     * @return Highlight_Model_Mapper_Interface
     */
    public function getMapper()
    {
        // maybe there was a crawler parameter in the url
        // we can try and find it from config.
        if($this->_getParam('mapper', false)) {
            $mapperName = $this->_getParam('mapper');
            $mapper = Highlight_Model_Mapper_Factory::get($mapperName);
        }

        // maybe the container is a named container and has a mapper in its config
        if($container = $this->_getContainer()) {
            if(!empty($container->name)) {
                $name = $container->name;
                $mapperName = Centurion_Config_Manager::get(sprintf('highlight.named_highlights.%s.mapper', $name));
                if($mapperName) {
                    $mapper = Highlight_Model_FieldMapper_Factory::get($mapperName);
                }
            }
        }
        
        // if everything else fails, get default mapper
        if(empty($mapper)) {
            $mapper = Highlight_Model_FieldMapper_Factory::get('default');
        }

        // some mappers may need the container we are talking about
        //$mapper->setContainer($this->_getContainer());
        return $mapper;
    }

    public function addAction()
    {
        $form = $this->_getAutocompleteForm();
        if($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $item = $form->save();
        }
        else {
            $this->view->errors = 'There was an error adding this content';
            $this->view->autoCompleteForm = $form;
            return $this->getAction();
        }
        
        $this->_returnTo();
    }
    
    public function deleteAction()
    {
        $rowId = $this->_getParam('row_id', null);
        $row = Centurion_Db::getSingleton('highlight/row')->findOneById($rowId);
        $row->delete();
        
        $this->_returnTo();
    }
    
    public function saveOrderAction()
    {
        $this->view->container = $container = $this->_getContainer();
        
        $rowTable = Centurion_Db::getSingleton('highlight/row');
        $i = 0;
        
        foreach ($this->_getParam('rowId') as $position => $id) {
            $row = $rowTable->findOneById($id);
            $row->position = $position;
            $row->save(); 
        }
        
        $this->_returnTo();
    }    
}
