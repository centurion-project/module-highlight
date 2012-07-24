<?php

/**
 * A factory that makes crawlers from config
 * it looks for the given crawler name in the config.
 * then instanciate the proper class
 * with the params as an array for only parameter
 **/
class Highlight_Model_Crawler_Factory
{
    static protected $_crawlers = array();
    
    private function __construct()
    {
    }

    static public function get($name = 'default')
    {
        if(!empty(self::$_crawlers[$name])) {
            return self::$_crawlers[$name];
        }

        $mapperConfig = Centurion_Config_Manager::get('highlight.crawlers.'.$name);

        if(empty($mapperConfig['className'])) {
            $className = 'Highlight_Model_Crawler_Default';
        }
        else {
            $className = $mapperConfig['className'];
        }

        if(!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Mapper class %s does not exist', $className));
        }

        self::$_crawlers[$name] = new $className($mapperConfig);
        return self::$_crawlers[$name];
    }
}

