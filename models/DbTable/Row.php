<?php
class Highlight_Model_DbTable_Row extends Centurion_Db_Table_Abstract
{
    protected $_name = 'highlight_row';
    protected $_rowClass = 'Highlight_Model_DbTable_Row_Row';
    
    protected $_referenceMap = array(
        'container' => array(
            'columns' => 'container_id',
            'refColumns' => 'id',
            'refTableClass' => 'Highlight_Model_DbTable_Container',
            'onDelete'  => self::CASCADE
        ),
        'proxy_content_type' => array(
            'columns' => 'proxy_content_type_id',
            'refColumns' => 'id',
            'refTableClass' => 'Core_Model_DbTable_ContentType',
            'onDelete'  => self::CASCADE
        ),
    );
}
