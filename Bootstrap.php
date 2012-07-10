<?php 
class Highlight_Bootstrap extends Centurion_Application_Module_Bootstrap
{
    public function _initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(
            new Highlight_Controller_Action_Helper_AddButtonOnGrid()
        );
        

    }

    public function _initContainers()
    {
        // creating all named highlights if they weren't already there
        $namedHighlights = Centurion_Config_Manager::get('highlight.named_highlights');
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

            foreach ($namedHighlights as $hl) {
                if(!isset($existing[$hl])) {
                    $containerModel->createWithName($hl);
                }
            }
        }
    }
}
