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

    
    /**
     * returns a human-readable name for the given row
     * @param Centurion_Db_Table_Row_Abstract $row the row for which we need a friendly name
     * @return string
     */
    public function friendlyName(Centurion_Db_Table_Row_Abstract $row)
    {
        $meta = $row->getTable()->getMeta();
        $verboseName = $meta['verboseName'];
        return sprintf('(%s) %s', $verboseName, $row->__toString());
    }
    
    /**
     * formats a result set from crawl (an array of rows) to an array of basic information used in autocomplete context
     * @param (string) $terms the terms for which to search
     * @param (array) $resultSet a result set from a call to `crawl`. if given, $terms is ignored
     * @return array an array of entries with the following keys
     *    `label`       a human readable name for the content
     *    `model`       the model name of the content
     *    `pk`          the primary key of the content
     */
    public function autocomplete($terms, $resultSet = null)
    {
        if(null === $resultSet) {
            $resultSet = $this->crawl(array('terms'=>$terms));
        }

        $res = array();
        foreach ($resultSet as $row) {
            $res[] = array(
                'label'     => $this->friendlyName($row),
                'model'     => get_class($row->getTable()),
                'pk'        => $row->pk
            );
        }
        return $res;
    }


    /**
     * Crawls the given table for the given terms in the given fields
     * the terms will get split into words. the matching contents are the ones containing at least one of the words
     * in the given fields
     * @param (Centurion_Db_Table_Abstract) $table the table in which to look for content
     * @param (array) $fields a list of fields names in which to look
     * @param (string) $terms a string contains terms or keywords to look for
     * @return [Centurion_Db_Table_Row_Abstract]
     */
    public function crawlTable(Centurion_Db_Table_Abstract $table, array $fields, $terms)
    {
        $select = $table->select(true);
        $this->selectKeywordsInTextFields($select, $fields, $terms);
        $all = $select->fetchAll();
        if($all->count() === 0) {
            return array();
        }

        return $this->rowsetToArray($all);

    }

    /** 
     * transforms a rowset object into an array of row
     * this is different from calling the `toArray` method on the rowset because the rows stay untouched
     * @param $rowset the rowset to transform
     * @return [Centurion_Db_Table_Row_Abstract]
     */
    public function rowsetToArray($rowset)
    {
        $res = array();
        foreach ($rowset as $r) {
            $res[] = $r;
        }
        return $res;
    }

    /** 
     * adds the corresponding where clauses to a select object in order to look for
     * rows containing at least of the the given keywords in the given fields
     * @param (Centurion_Db_Table_Select) $select the select to which we want to add the where clause
     * @param (array) $fields a list of field names in which to look
     * @param (string) $terms the keywords to look for
     * @return Centurion_Db_Table_Select
     */
    public function selectKeywordsInTextFields($select, $fields, $terms)
    {
        $columns = (array) $fields;

        // the 'where' clause we are adding actually contains a subrequest
        // this is because it is very difficult to manage OR operator with the Zend Select object
        $keywordsSelect = $select->getTable()->select(true);
        $keywordsSelect->reset(Zend_Db_Select::COLUMNS);
        $keywordsSelect->columns(array('id'));
        // split the terms parameter into words
        $terms = explode(' ', $terms);
        $first = true;
        foreach ($terms as $word) {
            if(empty($word)) {
                // if the word is empty, nothing to do
                continue;
            }
            // we are gonna add a LIKE condition for this word on each fields we have
            foreach ($columns as $column) {
                $filter = array(
                    $first ? '' : Centurion_Db_Table_Select::OPERATOR_OR,
                    $column,
                    Centurion_Db_Table_Select::RULES_SEPARATOR,
                    Centurion_Db_Table_Select::OPERATOR_CONTAINS
                );
                $filterString = implode($filter);
                $keywordsSelect->filter(array($filterString => '%'.$word.'%'));
                $first = false;
            }
        }
        // construct the subrequest expression from this select
        $expr = new Zend_Db_Expr('('.$keywordsSelect->__toString().')');
        // the subrequest returns a set of ids that match the keywords
        $select->filter(array('id__in' => $expr));

        return $select;
    }
}
