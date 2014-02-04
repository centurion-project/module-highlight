<?php

class Highlight_AdminRowController extends Centurion_Controller_CRUD //implements Translation_Traits_Controller_CRUD_Interface
{
    public function init()
    {
        $this->_helper->authCheck();
        $this->_helper->aclCheck();

        $this->_model = Centurion_Db::getSingleton('highlight/row');

        $this->_formClassName = 'Highlight_Form_Model_Row';

        /*$this->setOptions(array(
            'titleColumn'        =>  'title',
            'publishColumn'      =>  'is_published',
            'publishDateColumn'  =>  'published_at'
        ));*/
        

        $this->view->placeholder('headling_1_content')->set($this->view->translate('Manage Highlight@backoffice,cms'));
        $this->view->placeholder('headling_1_add_button')->set($this->view->translate('highlight@backoffice,cms'));
        
        //To conserv it into the request
        $this->_extraParam['container'] = $this->getRequest()->getParam('container',0);
        parent::init();
    }
    
    public function indexAction()
    {
        $this->_redirect($this->view->url(array('controller' => 'admin-highlight', 'module' => 'highlight'), 'default', true));
        die();
    }

    public function newAction()
    {
        $container = $this->_getContainer();
        $this->_getForm()->addElement('hidden', 'container_id', array('value'=>$container->id));

        parent::newAction();
    }

    public function postAction()
    {
        $container = $this->_getContainer();
        $this->_getForm()->addElement('hidden', 'container_id', array('value'=>$container->id));

        parent::postAction();
    }

    protected function _getContainer()
    {
        if (!isset($this->_container)) {
                $model = Centurion_Db::getSingleton('highlight/container');
                $this->_container = $model->findOneById($this->_extraParam['container']);
        }
        return $this->_container;
    }

}
