<?php

/**
 * Maps fields to something readable for highlights
 **/
class Highlight_Model_FieldMapper_Default implements Highlight_Model_FieldMapper_Interface
{
    
    protected $_fieldMap = array();
    protected $_pixelOnEmpty = false;

    function __construct($params)
    {
        unset($params['className']);
        
        foreach (array('title', 'link', 'description', 'cover') as $field) {
            if(!isset($params[$field]) || !is_array($params[$field]) || !isset($params[$field]['fields'])) {
                continue;
            }
            $fields = $params[$field]['fields'];
            if(!is_array($fields)) {
                continue;
            }

            $this->_pixelOnEmpty = $params['cover']['pixelOnEmpty'];

            $this->_fieldMap[$field] = $fields;
        }
    }

    /**
     * takes a row for an arbitrary content type and returns an array of fields to be used
     * to display the row as a Highlight
     * @param $row the row to map
     * @return array
     * The array returned must follow this structure
     * array(
     *     'title'          => (string)
     *     'link'           => (string) a relative or absolute url to the content
     *     'description'    => (string)
     *     'cover'          => (Media_Model_DbTable_Row_File) a centurion image object.
     * )
     */
    public function map(Centurion_Db_Table_Row_Abstract $row)
    {
        $res = array('row'=>$row);

        $textFields = array('title', 'link', 'description');
        foreach ($textFields as $textField) {
            $value = null;
            foreach ($this->_fieldMap[$textField] as $field) {
                if(isset($row->{$field}) && is_string($row->{$field})) {
                    $value = $row->{$field};
                    break;
                }
            }
            $res[$textField] = $value;
        }

        if (!isset($res['title'])) {
            $res['title'] = $row->__toString();
        }

        foreach ($this->_fieldMap['cover'] as $field) {
            if(isset($row->{$field}) && $row->{$field} instanceof Media_Model_DbTable_Row_File) {
                $res['cover'] = $row->{$field};
            }
        }
        if(!isset($res['cover']) && $this->_pixelOnEmpty) {
            $res['cover'] = Centurion_Db::getSingleton('media/file')->getPx();
        }

        return $res;
    }
}
