<?php

class Highlight_Test_Model_Crawler_Crawler extends Highlight_Model_Crawler_Abstract
{
    protected $_table;


    public function getTable()
    {
        if(!is_null($this->_table)) return $this->_table;
        $this->_table = new Highlight_Test_Model_DbTable_Crawlable();
        return $this->_table;
    }


    public function crawl(array $params=null)
    {
        return $this->crawlTable($this->getTable(), $params['fields'], $params['terms']);
    }

}

