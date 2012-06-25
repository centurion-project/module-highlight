<?php
class Highlight_Model_DbTable_Feed extends Centurion_Db_Table_Abstract
{
    protected $_name = 'highlight_feed';
    protected $_rowClass = 'Highlight_Model_DbTable_Row_Feed';
    
    protected $_referenceMap = array(
        'proxy_content_type' => array(
            'columns' => 'proxy_content_type_id',
            'refColumns' => 'id',
            'refTableClass' => 'Core_Model_DbTable_ContentType'
        )
    );
}