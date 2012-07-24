<?php

/**
 * A form to add a new item in the collection for a highlight
 */
class Highlight_Form_Model_Item extends Centurion_Form_Model_Abstract
{
    /**
     * the container to which this item belongs
     */
    protected $_container = null;

    public function __construct($options = array(), Centurion_Db_Table_Abstract $instance = null)
    {
        $this->_model = Centurion_Db::getSingleton('highlight/row');
        
        $this->_exclude = array('id', 'container_id', 'cover_id');
        
        $this->_elementLabels = array(
            'proxy_pk'              => 'id',
            'proxy_content_type_id' => 'model',
            'position'              => 'position'
        );
        
        parent::__construct($options, $instance);
    }

    public function init()
    {
        parent::init();

        $this->setAttrib('class', 'form-main form-highlight');

        // make this element a hidden value
        $proxy_pk = $this->getElement('proxy_pk');
        $this->removeElement('proxy_pk');
        $this->addElement('hidden', 'proxy_pk', array('required'=>true));
        $this->getElement('proxy_pk')->setRequired(true);

        // make this element a hidden value
        $proxy_content_type_id = $this->getElement('proxy_content_type_id');
        $this->removeElement('proxy_content_type_id');
        $this->addElement('hidden', 'proxy_content_type_id', array('required'=>true));
        $this->getElement('proxy_content_type_id')->setRequired(true);

        // make this element a hidden value
        $position = $this->getElement('position');
        $this->removeElement('position');
        $this->addElement('hidden', 'position', array(
            'validators'        => $position->getValidators()
        ));
        $this->getElement('position')->setRequired(true);


        // add the field in which the autocomplete plugin will be affected
        $this->addElement('text', 'autocomplete', array(
            'class'     => 'ui-autocomplete-input ui-autocomplete-loading',
            'role'      => 'textbox',
            'arie-haspopup' => 'true',
            'label'     => $this->_translate('Content'),
            'description'   => $this->_translate('Search for content')
        ));
        // we need a special render helper to render this element. cause Centurion
        // doesn't use form decorators as it should
        $this->getElement('autocomplete')->helper = 'highlightAutoCompleteTextForm';

        // group fields
        $elements = array_keys(array_merge($this->getElements(), $this->getSubForms()));
        $this->addDisplayGroup($elements, 'allElements', array('class'=>'form-group'));
        $this->getDisplayGroup('allElements')->setLegend($this->_translate('Add a highlight'));
    }


    /** 
     * Set the container for this item
     */
    public function setContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    /** 
     * Get the container for this item
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /** 
     * Completely override default Centurion_Form_Model_Abstract behaviour.
     * because addRow is actually tested on the highlight model
     */
    public function save($adapter = null)
    {
        $container = $this->getContainer();
        // forbid saving an item with no container
        if(is_null($container)) {
            throw new UnexpectedValueException('no container found for this form');
        }

        // find the proxy row and add it to the collection
        $model = Centurion_Db::getSingletonByClassName($this->getElement('proxy_content_type_id')->getValue());
        $row = $model->find($this->getElement('proxy_pk')->getValue())->current();
        $item = $container->addRow($row, $this->getElement('position')->getValue());

        return $item;
    }
}
