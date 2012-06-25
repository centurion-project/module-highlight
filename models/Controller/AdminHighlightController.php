<?php
abstract class Highlight_Model_Controller_AdminHighlightController extends Centurion_Controller_Action
{
    protected $_container = null;
    
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
        $this->view->containers = Centurion_Db::getSingleton('highlight/container')->fetchAll();
    }
    
    protected function _getContainer($redirect404 = true)
    {
        if (!isset($this->_container)) {
            if (($container = $this->_getParam('container', null)) && ($container instanceof Highlight_Model_DbTable_Row_Container)) {
                $this->_container = $container;
            } else if ($id = $this->_getParam('id', null)) {
                $this->_container = $this->_helper->getObjectOr404('highlight/container', array('id' =>  $this->_getParam('id')));
            } else {
                throw new Zend_Controller_Action_Exception(sprintf('No container given'), 404);
            }
        }
        return $this->_container;
    }
    
    public function autocompleteAction()
    {
        $container = $this->_getContainer();
        
        $highlight = $container->getHighlightModel();
        $resultArray = $highlight->autocomplete($this->_getParam('term'), $container);

        $response = $this->getResponse();
        
        $this->_helper->Json($resultArray);
        
//        $response->appendBody(Zend_Json::encode($resultArray));
    }
    
    public function getAction()
    {
        $this->view->container = $this->_getContainer();
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
