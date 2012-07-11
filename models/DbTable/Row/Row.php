<?php

class Highlight_Model_DbTable_Row_Row extends Centurion_Db_Table_Row_Proxy
{

    public function getBlockHtml()
    {
        $meta = $this->getProxy()->getTable()->getMeta();
        return sprintf('<span>%s</span>', $meta['verboseName']);
    }
    
}
