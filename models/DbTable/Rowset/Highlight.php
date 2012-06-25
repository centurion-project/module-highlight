<?php
class Highlight_Model_DbTable_Rowset_Highlight implements SeekableIterator, Countable, ArrayAccess
{
    protected $_name = null;
    protected $_container = null;
    protected $_count = null;
    protected $_rowCount = null;
    protected $_feedCount = null;
    protected $_data = null;
    
    public function __construct($container)
    {
        $this->_container = $container['container'];
        $this->_name = $container['name'];
        $this->init();
    }
    
    function __get($column)
    {
        if ('_row_set' === $column) {
            $this->_row_set = $this->_container->findDependentRowsetOrdered('row_set', 'position');
            return $this->_row_set;
        }
    }
    
    /**
     * Store data, class names, and state in serialized object
     *
     * @return array
     */
    public function __sleep()
    {
        return array('_container');
    }

    /**
     * @return void
     */
    public function __wakeup()
    {
        
    }
    
    public function getContainer()
    {
        return $this->_container;
    }
    
    /**
     * Returns the number of elements in the collection.
     *
     * Implements Countable::count()
     *
     * @return int
     */
    public function count()
    {
        if (null === $this->_count) {
            $this->_count = $this->getRowCount() + $this->getFeedCount();
        }
        
        return $this->_count;
    }
    
    public function getRowCount()
    {
        if (null === $this->_rowCount) {
            $this->_rowCount = count($this->_row_set);
        }
        return $this->_rowCount;
    }
    
    public function getFeedCount()
    {
        if (null === $this->_feedCount) {
            $this->_feedCount = count($this->_container->feed);
        }
        return $this->_feedCount;
    }
    
    /**
     * Iterator pointer.
     *
     * @var integer
     */
    protected $_pointer = 0;

    protected $_rows = array();

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Return the connected state of the rowset.
     *
     * @return boolean
     */
    public function isConnected()
    {
        throw new Exception('not applicable');
        return $this->_connected;
    }

    /**
     * Returns the table object, or null if this is disconnected rowset
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        throw new Exception('not applicable');
        return $this->_table;
    }

    /**
     * Set the table object, to re-establish a live connection
     * to the database for a Rowset that has been de-serialized.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return boolean
     * @throws Zend_Db_Table_Row_Exception
     */
    public function setTable(Zend_Db_Table_Abstract $table)
    {
        throw new Exception('not applicable');

    }

    /**
     * Query the class name of the Table object for which this
     * Rowset was created.
     *
     * @return string
     */
    public function getTableClass()
    {
        throw new Exception('not applicable');
        return $this->_tableClass;
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Zend_Db_Table_Rowset_Abstract Fluent interface.
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Zend_Db_Table_Row_Abstract current element from the collection
     */
    public function current()
    {
        return $this->offsetGet($this->_pointer);
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int
     */
    public function key()
    {   
        return $this->_pointer;
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function next()
    {
        ++$this->_pointer;
    }
    
    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function previous()
    {
        --$this->_pointer;
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        return $this->offsetExists($this->_pointer);
    }

    

    /**
     * Take the Iterator to position $position
     * Required by interface SeekableIterator.
     *
     * @param int $position the position to seek to
     * @return Zend_Db_Table_Rowset_Abstract
     * @throws Zend_Db_Table_Rowset_Exception
     */
    public function seek($position)
    {
        $position = (int) $position;
        if (!$this->offsetExists($position)) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new Zend_Db_Table_Rowset_Exception("Illegal index $position");
        }
        $this->_pointer = $position;
        return $this;
    }

    /**
     * Check if an offset exists
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return !($offset < 0 || $offset >= $this->count());
    }

    /**
     * Get the row for the given offset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return Zend_Db_Table_Row_Abstract
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset) === false) {
            return null;
        }
        
        if ($offset < $this->getRowCount()) {
            return $this->_row_set[$offset];
        }
        
        if ($offset < $this->getRowCount() + $this->getFeedCount()) {
            return $this->_container->feed->getRow($offset - $this->getRowCount());
        }
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Returns a Zend_Db_Table_Row from a known position into the Iterator
     *
     * @param int $position the position of the row expected
     * @param bool $seek wether or not seek the iterator to that position after
     * @return Zend_Db_Table_Row
     * @throws Zend_Db_Table_Rowset_Exception
     */
    public function getRow($position, $seek = false)
    {
        $key = $this->key();
        try {
            $this->seek($position);
            $row = $this->current();
        } catch (Zend_Db_Table_Rowset_Exception $e) {
            throw new Zend_Db_Table_Rowset_Exception('No row could be found at position ' . (int) $position, 0, $e);
        }
        if ($seek == false) {
            $this->seek($key);
        }
        return $row;
    }

    /**
     * Returns all data as an array.
     *
     * Updates the $_data property with current row object values.
     *
     * @return array
     */
    public function toArray()
    {
        if (null === $this->_data) {
            foreach ($this as $i => $row) {
                $this->_data[$i] = $row->toArray();
            }
        }
        return $this->_data;
    }
    
}
