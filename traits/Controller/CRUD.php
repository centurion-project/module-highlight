<?php

/**
 * A traits that add functionnality to crud controllers in order to manage highlights
 * it mainly adds a link to manage the highlights of a given row in the grid view
 **/
class Highlight_Traits_Controller_CRUD extends Centurion_Traits_Controller_CRUD_Abstract
{
    public function __construct($controller) 
    {
        parent::__construct($controller);
    }

    public function init()
    {
        parent::init();
        try {
            $displays = $this->_displays;
            $displays['manage_highlights'] = array(
                'label'             => $this->view->translate('Highlights'),
                'sort'              => false,
                'type'              => Centurion_Controller_CRUD::COLS_CALLBACK,
                'callback'          => array($this, 'displayManageLink')
            );
            $this->_displays = $displays;
        }
        catch(Exception $e) {
            // something went wrong. Maybe the model doesn't implement the highlight traits
        }
    }


    public function displayManageLink($row)
    {
        list($contentType,) = Centurion_Db::getSingleton('core/content_type')->getOrCreate(array(
            'name'=>get_class($row->getTable())
        ));
        $controller = isset($this->_controller->highlightController) ? $this->_controller->highlightController : 'admin-highlight';
        $module = isset($this->_controller->highlightModule) ? $this->_controller->highlightModule : 'highlight';
        
        $params = array(
            'module'            => $module,
            'controller'        => $controller,
            'action'            => 'get',
            'proxy_pk'          => $row->pk,
            'proxy_content_type_id' => $contentType->id,
            //'returnafter'          => $this->view->url()
        );
        $params = array_merge($params, $this->_getExtraParams());

        $url = $this->view->url($params);

        $label = $this->view->translate('Manage highlights');
        return sprintf('<a target="_blank" href="%s">%s</a>', $url, $label);
    }

    protected function _getExtraParams()
    {
        if(!method_exists($this->_controller, 'getAdditionalHighlightUrlParams')) {
            return array();
        }

        return $this->_controller->getAdditionalHighlightUrlParams();
        
    }

}
