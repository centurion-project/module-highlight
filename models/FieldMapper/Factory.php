<?php

/**
 * 
 **/
class Highlight_Model_FieldMapper_Factory
{
    static protected $_mappers = array();
    
    private function __construct()
    {
    }

    static public function get($name = 'default')
    {
        if(!empty(self::$_mappers[$name])) {
            return self::$_mappers[$name];
        }

        $mapperConfig = Centurion_Config_Manager::get('highlight.mappers.'.$name);

        if(empty($mapperConfig['className'])) {
            $className = 'Highlight_Model_FieldMapper_Default';
        }
        else {
            $className = $mapperConfig['className'];
        }

        if(!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Mapper class %s does not exist', $className));
        }

        self::$_mappers[$name] = new $className($mapperConfig);
        return self::$_mappers[$name];
    }
}
