<?php
class Highlight_Model_DbTable_Row_Container extends Centurion_Db_Table_Row_Proxy
{
    protected $_highlightModel = null;
    
    public function init()
    {
        $this->_specialGets['row_set'] = 'getRowSet';
        $this->_specialGets['title'] = 'titleGetter';
    }
    
    /**
     * get a rowset of the highlight items attached to this container
     * @return Centurion_Db_Table_Rowset_Abstract
     */
    public function getRowSet()
    {
        return Centurion_Db::getSingleton('highlight/row')->select(true)->filter(array('container_id' => $this->id))->order('position asc')->fetchAll();
    }
    
    /**
     * add a row to this container at the given position
     * @param $row the proxy to add
     * @param $position the position at which to add the proxy
     */
    public function addRow($row, $position)
    {
        $this->getTable()->addRow($this, $row, $position);
    }
    
    /**
     * get a human readable string for the given row
     */
    public function getTitle($row)
    {
        return $row->__toString();
    }

    /**
     * @deprecated use field mappers instead
     */
    public function titleGetter()
    {
        if(null === $this->name) {
            return $this->getTitle($this->getProxy());
        }
        else {
            return $this->name;
        }
    }

    public function getHighlights($mapper = null)
    {
        if(!$mapper) {
            $mapper = 'default';
        }
        if(is_string($mapper)) {
            $mapper = Highlight_Model_Mapper_Factory::get('default');
        }
        if(!($mapper instanceof Highlight_Model_Mapper_Interface)) {
            throw new InvalidArgumentException('given mapper does not implement the mapper interface');
        }

        $rowset = $this->getRowSet();
        $res = array();
        foreach ($rowset as $row) {
            $res[] = $row->map($mapper);
        }

        return $res;
    }

}
