<?php
class Highlight_Model_DbTable_Container extends Centurion_Db_Table_Abstract
{
    protected $_name = 'highlight_container';
    protected $_rowClass = 'Highlight_Model_DbTable_Row_Container';
    
    public function addRow($container, $row, $position)
    {
        if ($container instanceof Centurion_Db_Table_Row_Abstract) {
            $container = $container->id;
        }
        
        list($highlightRow, $created) = Centurion_Db::getSingleton('highlight/row')->getOrCreate(array('container_id' => $container, 'position' => $position));
        $highlightRow->setProxy($row);
        $highlightRow->save();
    }
    
    protected $_referenceMap = array(
        'proxy_model' => array(
            'columns' => 'proxy_model_id',
            'refColumns' => 'id',
            'refTableClass' => 'Core_Model_DbTable_ContentType'
        ),
    );
}
