<?php

/**
 * 
 **/
class Highlight_Model_Crawler_Generic extends Highlight_Model_Crawler_Abstract
{

    public function crawl(array $params=null)
    {
        return $this->crawlTable(Centurion_Db::getSingleton('cms/flatpage'), array('title'), $params['terms']);
    }
    
}
