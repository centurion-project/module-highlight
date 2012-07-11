<?php

/**
 * a simple crawler that only looks in flatepages
 **/
class Highlight_Model_Crawler_Generic extends Highlight_Model_Crawler_Abstract
{
    protected $_table = null;
    protected $_fields = array('title');
    public function __construct(Centurion_Db_Table_Abstract $table=null, array $fields = array('title'))
    {
        if(is_null($table)) $table = Centurion_Db::getSingleton('cms/flatpage');
        $this->_fields = $fields;
        $this->_table = $table;
        parent::__construct();
    }

    public function crawl(array $params=null)
    {
        return $this->crawlTable($this->_table, $this->_fields, $params['terms']);
    }
    
}
