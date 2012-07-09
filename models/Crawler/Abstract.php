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

    public function rowsetToArray($rowset)
    {
        $res = array();
        foreach ($rowset as $r) {
            $res[] = $r;
        }
        return $res;
    }

    public function selectKeywordsInTextFields($select, $fields, $terms)
    {
        $columns = (array) $fields;

        $keywordsSelect = $select->getTable()->select(true);
        $keywordsSelect->reset(Zend_Db_Select::COLUMNS);
        $keywordsSelect->columns(array('id'));
        $terms = explode(' ', $terms);
        $first = true;
        foreach ($terms as $word) {
            if(empty($word)) {
                continue;
            }
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
        $expr = new Zend_Db_Expr('('.$keywordsSelect->__toString().')');
        $select->filter(array('id__in' => $expr));

        return $select;
    }
}
