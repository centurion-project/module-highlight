<?php

/**
 * @deprecated
 */
abstract class Highlight_Model_Highlight_Abstract
{
    const LIMIT_AUTOCOMPLETE = 10;
    
    abstract protected function _autocomplete($term, $container);
    
    public function filterOnContainer(Zend_Db_Table_Select $select, $container)
    {
        list($contentType, ) = Centurion_Db::getSingleton('core/contentType')->getOrCreate(array('name' => get_class($select->getTable())));
        return $select->where(new Zend_Db_Expr('not exists (select 1 from highlight_row where '.$select->getTable()->info(Zend_Db_Table_Abstract::NAME).'.id = proxy_pk and container_id = '.$container->id.' and proxy_content_type_id = ' . $contentType->id . ')'));
    }
    
    public function autocomplete($term, $container)
    {
        $rowSet = $this->_autocomplete($term, $container);
        
        $result = array();
        
        foreach ($rowSet as $row) {
            $result[] = array('model' => get_class($row), 'pk' => $row->id, 'label' => $row->__toString());
        }
        
        return $result;
    }
    
    public function getFilter()
    {
        
    }
    
    public function getTitle($row)
    {
        return $row->__toString();
    }
}
