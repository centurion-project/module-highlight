<?php
class Highlight_Model_DbTable_Row_Feed extends Centurion_Db_Table_Row_Abstract implements Countable
{
    protected $_count = null;
    protected $_rows = array();
    
    public function getRow($offset)
    {
        if (!$this->isValid($offset)) {
            return null;
        }
        
        if (!isset($this->_rows[$offset])) {
            //FetchRow remove the limit(1, $offset)
            $this->_rows[$offset] = $this->getSelect()->limit(1, $offset)->fetchAll()->current();
        }
        
        return $this->_rows[$offset];
    }
    
    public function count()
    {
        if (null === $this->_count) {            
            $this->_count = $this->getSelect()->count();
        }
        return $this->_count;
    }
    /**
     * @return Centurion_Db_Table_Select
     */
    public function getSelect()
    {
        if (in_array('Centurion_Db_Table_Select', class_parents($this->proxy_content_type->name))) {
            $select = new $this->proxy_content_type->name();
        } elseif (in_array('Centurion_Db_Table_Abstract', class_parents($this->proxy_content_type->name))) {
            $select = Centurion_Db::getSingletonByClassName($this->proxy_content_type->name)->select(true);
        } else {
            throw new Exception('Invalid type of flux');
        }
        
        if (null !== $this->order)
            $select->order($this->order);
            
        if (null !== $this->where) {            
            $whereParam = (array) json_decode($this->where);
            
            //This will fail on expr, custom...
            $select->filter($whereParam);
        }

        return $select;
    }
    
    public function isValid($offset)
    {
        return $offset < $this->count();
    }
}