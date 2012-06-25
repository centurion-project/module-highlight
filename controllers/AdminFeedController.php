<?php
class Highlight_AdminFeedController extends Centurion_Controller_Action
{
    public function createFeedAction()
    {
        if ($this->_request->isPost()) {
            $this->view->form = new Highlight_Form_Model_Feed();
            
            $values = $this->_request->getPost();
            
            if (isset($values['name'])&&$this->view->form->isValid($values)) {
                $this->view->form->save();
                $this->getHelper('redirector')->setGotoUrl($this->view->url(array('action' => 'index')));
            }
            
            if (isset($values['current_filter']) && isset($values['current_sortCol']) && isset($values['current_sortCol']) && isset($values['current_sortOrder']) && isset($values['model'])) {
                $this->view->form->setFilter($values['current_filter']);
                $this->view->form->setSort($values['current_sortCol'], $values['current_sortOrder']);
                $this->view->form->setModelname($values['model']);
            }
            
            $this->view->form->cleanForm();
        }
    }
    
    public function indexAction()
    {
        
    }
}