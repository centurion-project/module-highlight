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
    
    public function actions($row)
    {
        return '<a href="'.$this->view->url(array('id' => $row->id, 'module' => 'highlight', 'controller' => 'admin-highlight'), 'rest', true).'">'.$this->view->translate('Manage highlight').'</a>';
    }
    
    public function init()
    {
        
    }
    
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

    public function getContainers()
    {
        $select = $this->getSelect();
        return $select->fetchAll();
    }

    public function getSelect()
    {
        if($this->_select) return $this->_select;
        $this->_select = Centurion_Db::getSingleton('highlight/container')->select(true);
        return $this->_select;
    }
    
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

    protected function _getProxy()
    {
        $ctype = Centurion_Db::getSingleton('core/content_type')->findOneById($this->_getParam('proxy_content_type_id'));
        $model = Centurion_Db::getSingletonByClassName($ctype->name);
        $row = $model->findOneById($this->_getParam('proxy_pk'));
        return $row;
    }

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

    public function getCrawler()
    {
        return new Highlight_Model_Crawler_Generic();
    }
    
    public function getAction()
    {
        $this->view->container = $this->_getContainer();
        if($backUrl = $this->_getParam('returnafter', false)) {
            $this->view->backUrl = urldecode($backUrl);
        }
        $this->render('admin-highlight/get', true, true);

    }
    
    public function addAction()
    {
        $className = $this->_getParam('model', null);
        $pk = $this->_getParam('pk', null);
 
        $container = $container = $this->_getContainer();

        if (trim($className) !== '' && trim($pk) !== '') {
            $row = Centurion_Db::getSingletonByClassName($className)->find($pk)->current();
            $container->addRow($row, $this->_getParam('position', null));
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
