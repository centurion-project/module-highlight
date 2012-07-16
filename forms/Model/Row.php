<?php

class Highlight_Form_Model_Row extends Centurion_Form_Model_Abstract // implements Translation_Traits_Form_Model_Interface
{

    public function __construct($options = array(), Centurion_Db_Table_Row_Abstract $instance = null)
    {
        $this->_model = Centurion_Db::getSingleton('highlight/row');

        $this->_exclude = array('id', 'proxy_px', 'proxy_content_type_id', 'cover_id', 'position');

        $this->_elementLabels = array(
            'title'                     => $this->_translate('Title'),
            'link'                     => $this->_translate('Link'),
            'link_label'                     => $this->_translate('Link label'),
            'cover_id'                     => $this->_translate('Image'),
            'description'                     => $this->_translate('Description'),
            'body'                     => $this->_translate('Body'),
        );

        parent::__construct($options, $instance);
    }

    public function init()
    {
        parent::init();

        $this->getElement('title')->setDescription($this->_translate('Title'));

        $this->setLegend($this->_translate('Edit Highlight info'));

        $this->getElement('body')->setAttrib('class', 'field-rte')
                                 ->setAttrib('large', true)
                                 ->removeFilter('StripTags');


        $pic = new Media_Form_Model_Admin_File(array('name' => 'cover'));
        $pic->getFilename()->getValidator('Size')->setMax(4*1024*1024);
        $this->addReferenceSubForm($pic, 'cover');


        //$mapper = Highlight_Model_FieldMapper_Factory::get('default');
        //$map = $mapper->map($this->_proxy);
    }

    public function _onPopulateWithInstance()
    {
        $this->removeElement('container_id');
    }

}
