<?php

/**
 * Abstract crawler class.
 * A crawler is an object on which you can call the method `crawl` with a given query information.
 * it then returns a collection of rows that match this query according to the given criteria
 * the rows returned can be of any type
 **/
abstract class Highlight_Model_Crawler_Abstract
{
    
    protected $_models = array();

    public function __construct() {
    }

    /**
     * crawl models for the given criteria. returns an array of rows
     * @returns [Centurion_Db_Table_Row_Abstract]
     */
    abstract public function crawl(array $params = null); 
}
