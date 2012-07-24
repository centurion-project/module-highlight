<?php

/**
 * A factory that makes field mappers from config
 * @todo config structure
 **/
class Highlight_Model_FieldMapper_Factory
{
    static protected $_mappers = array();
    
    private function __construct()
    {
    }

    static public function get($name = 'default', $override = null)
    {
        if(!empty(self::$_mappers[$name])) {
            return self::$_mappers[$name];
        }

        $mapperConfig = Centurion_Config_Manager::get('highlight.mappers.'.$name);

        if(!empty($override) && is_array($override)) {
            $mapperConfig = self::_mergeArrays($mapperConfig, $override);
        }

        if(empty($mapperConfig['className'])) {
            $className = 'Highlight_Model_FieldMapper_Default';
        }
        else {
            $className = $mapperConfig['className'];
        }

        if(!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Mapper class %s does not exist', $className));
        }

        //self::$_mappers[$name] = new $className($mapperConfig);
        return new $className($mapperConfig);
    }


    static protected function _mergeArrays($Arr1, $Arr2)
    {
        foreach($Arr2 as $key => $Value) {
            if(array_key_exists($key, $Arr1) && is_array($Value))
            $Arr1[$key] = static::_mergeArrays($Arr1[$key], $Arr2[$key]);

            else
            $Arr1[$key] = $Value;
        }

        return $Arr1;

    }
}
