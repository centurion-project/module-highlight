<?php

class Highlight_Form_Model_Feed extends Centurion_Form_Model_Abstract
{   
    public function __construct($options = array(), Centurion_Db_Table_Abstract $instance = null)
    {
        $this->_model = Centurion_Db::getSingleton('highlight/feed');
        
        $this->_exclude = array('id', 'proxy_content_type_id', 'order', 'where', 'hightlight_container_id');
        
        $this->_elementLabels = array(
            'name' => 'Name'
        );
        
        parent::__construct($options, $instance);
    }
    
    public function setFilter($filter)
    {
        $this->getElement('where')->setValue($filter);
    }
    
    public function setSort($sortCol, $sortOrder)
    {
        if ($sortCol !== '1')
            $this->getElement('order')->setValue($sortCol . ' ' . $sortOrder);
    }
    
    public function setModelname($model)
    {
        list($contentTypeRow, $created) = Centurion_Db::getSingleton('centurion/contentType')->getOrCreate(array('name' => $model));
        $this->getElement('proxy_content_type_id')->setValue($contentTypeRow->id);
    }
    
    public function init()
    {
        parent::init();
        
        $this->addElement('hidden', 'where', array());
        $this->addElement('hidden', 'order', array());
        $this->addElement('hidden', 'proxy_content_type_id', array());
        
        $this->getElement('name')->addValidator('Db_NoRecordExists', false, array('table' => 'highlight_feed', 'field' => 'name'));
    }
    
    public function save($adapter = null)
    {
        $this->_exclude = array('hightlight_container_id');
        parent::save($adapter);
    }
}