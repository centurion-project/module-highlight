<?php
class Highlight_Model_DbTable_Row_Container extends Centurion_Db_Table_Row_Proxy
{
    protected $_highlightModel = null;
    
    public function init()
    {
        $this->_specialGets['row_set'] = 'getRowSet';
    }
    
    public function getRowSet()
    {
        return Centurion_Db::getSingleton('highlight/row')->select(true)->filter(array('container_id' => $this->id))->order('position asc')->fetchAll();
    }
    
    public function getHighlightModel()
    {
        if (null === $this->_highlightModel) {
            
            $this->_highlightModel = $this->getTable();
        }
        
        return $this->_highlightModel;
    }
    
    public function addRow($row, $position)
    {
        $this->getTable()->addRow($this, $row, $position);
    }
    
    public function getTitle($row)
    {
        return $this->getHighlightModel()->getFullDescription($row);
    }

}
