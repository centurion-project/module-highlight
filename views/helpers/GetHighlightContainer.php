<?php

class Highlight_View_Helper_GetHighlightContainer extends Zend_View_Helper_Abstract
{
    public function getHighlightContainer($containerName)
    {
        $table = Centurion_Db::getSingleton('highlight/container');
        
        Centurion_Cache_TagManager::addTag($table);
        Centurion_Cache_TagManager::addTag(Centurion_Db::getSingleton('highlight/row'));
        
        $containerRow = Centurion_Db::getSingleton('highlight/container')->findOneByName($containerName);
        
        if ($containerRow === null) {
            throw new Exception(sprintf('Container %s doesn\'t exists', $containerName));
        }
        
        return new Highlight_Model_DbTable_Rowset_Highlight(array('container' => $containerRow, 'name' => $containerName));
    }
}