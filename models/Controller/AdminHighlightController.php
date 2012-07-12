<?php
abstract class Highlight_Model_Controller_AdminHighlightController extends Centurion_Controller_Action
{
    protected $_container = null;
    protected $_select = null;
    
    public function preDispatch()
    {
        $view   = $this->initView();
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
    
    public function init()
    {
        
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
    
    public function indexAction()
    {
        $this->view->containers = $this->getContainers();

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
        return $this->_select;
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
            return $proxy->getCrawler();
        }
        
        // maybe there was a crawler parameter in the ur
        // we can try and find it from config.
        if($this->_getParam('crawler', false)) {
            $crawlerName = $this->_getParam('crawler');
            $crawlerClass = Centurion_Config_Manager::get(sprintf('highlight.crawlers.%s.className', $crawlerName));
            if($crawlerClass && class_exists($crawlerClass)) {
                $crawlerParams = Centurion_Config_Manager::get(sprintf('highlight.crawlers.%s.params', $crawlerName));
                if($crawlerParams) {
                    return new $crawlerClass($crawlerParams);
                }
                else {
                    return new $crawlerClass();
                }
            }
        }


        return new Highlight_Model_Crawler_Generic();
    }

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

        $this->view->highlightMapper = $this->_getHighlightMapper();

        $this->render('admin-highlight/get', true, true);
    }

    protected function _getHighlightMapper()
    {
        // return default mapper for now
        return Highlight_Model_FieldMapper_Factory::get();
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
