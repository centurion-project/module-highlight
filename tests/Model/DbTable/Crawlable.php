<?php

class Highlight_Test_Model_DbTable_Crawlable extends Asset_Model_DbTable_Abstract
{
    protected $_name = 'highlight_crawlable';

    protected function _createTable()
    {
        $this->getDefaultAdapter()->query(<<<EOS
            CREATE TABLE IF NOT EXISTS highlight_crawlable (
            `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `title` VARCHAR( 255 ) NOT NULL,
            `slug` VARCHAR( 255 ) DEFAULT NULL,
            `DESCRIPTION` VARCHAR( 255 ) NULL
            ) ENGINE = INNODB;
EOS
        );
    }

    protected function _destructTable()
    {
        $this->getDefaultAdapter()->query('Drop table highlight_crawlable');
    }
}
