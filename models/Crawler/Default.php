<?php

/**
 * a simple crawler that only looks in the models defined in the config
 * @todo define config structure
 **/
class Highlight_Model_Crawler_Default extends Highlight_Model_Crawler_Abstract
{
    protected $_models = array();

    public function __construct(array $params)
    {
        unset($params['className']);

        $this->_useFilters = true;
        if(isset($params['useFilters'])) {
            $this->_useFilters = $params['useFilters'];
        }
        unset($params['useFilters']);

        if(!isset($params['models']) || empty($params['models'])) {
            throw new InvalidArgumentException('No models defined for this crawler');
        }
        if(!is_array($params['models'])) {
            throw new InvalidArgumentException('models parameter is not an array');
        }

        $this->_models = $params['models'];

        parent::__construct($params);
    }

    public function crawl(array $params=null)
    {
        $res = array();
        foreach ($this->_models as $model) {
            if(!isset($model['table']) || !isset($model['fields'])) {
                continue;
            }
            $table = Centurion_Db::getSingleton($model['table']);
            $fields = (array) $model['fields'];
            $limit = (isset($model['limit']) && is_numeric($model['limit'])) ? $model['limit'] : 0;

            Centurion_Db_Table_Abstract::setFiltersStatus($this->_useFilters);
            $res = array_merge($res, $this->crawlTable($table, $fields, $params['terms'], $limit));
            Centurion_Db_Table_Abstract::restoreFiltersStatus();
        }
        return $res;
    }

}
