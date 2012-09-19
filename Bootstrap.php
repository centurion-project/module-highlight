<?php 
class Highlight_Bootstrap extends Centurion_Application_Module_Bootstrap
{
    public function _initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new Highlight_Controller_Action_Helper_AddButtonOnGrid()
        );
        

    }

    /**
     * reads the config file for named containers
     * if some don't already exist in base, create them
     * the config authorizes numeric or string keys for the named highlights config container
     */
    public function _initContainers()
    {
        // creating all named highlights if they weren't already there
        $namedHighlights = self::listNamedHighlightsInConfig();
        if(is_array($namedHighlights) && count($namedHighlights)) {
            $containerModel = Centurion_Db::getSingleton('highlight/container');
            $allNamed = $containerModel->select(true)->filter(array(
                'proxy_pk__isnull' => true,
                'proxy_content_type_id__isnull' => true
            ));
            $allNamed = $allNamed->fetchAll();
            $existing = array();
            foreach ($allNamed as $container) {
                $existing[$container->name] = true;
            }

            foreach ($namedHighlights as $name) {
                if(!isset($existing[$name])) {
                    $containerModel->createWithName($name);
                    $existing[$name] = true;
                }
            }
        }
    }


     
    /**
     * reads all the named highlights from config and returned them in alphabetical order
     * @return [string]
     */
    static public function listNamedHighlightsInConfig()
    {
        $namedHighlights = Centurion_Config_Manager::get('highlight.named_highlights', array());
        $res = array();
        foreach ($namedHighlights as $key => $name) {
            // if the value is an array, then the name is the key
            if(is_array($name)) {
                $res[] = $key;
            }
            // else the name is the value
            else {
                $res[] = $name;
            }
        }
        sort($res);
        return $res;
    }
}
