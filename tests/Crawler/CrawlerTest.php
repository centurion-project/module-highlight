<?php

require_once dirname(__FILE__) . '/../../../../../tests/TestHelper.php';

class Highlight_Test_Crawler_CrawlerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }
    
    protected function tearDown()
    {
    }

    public function getCrawlableTable()
    {
        return Centurion_Db::getSingletonByClassname('Highlight_Test_Model_DbTable_Crawlable');
    }

    public function testFindTextFields()
    {
        $table = $this->getCrawlableTable();
        $crawler = $this->getCrawler();

        array('title', 'slug', 'description');
    }
}

