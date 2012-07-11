<?php

class Highlight_View_Helper_GetHighlightContainer extends Zend_View_Helper_Abstract
{
    /**
     * returns the highlight container for the given name
     * @todo find container from a proxy as well
     */
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
