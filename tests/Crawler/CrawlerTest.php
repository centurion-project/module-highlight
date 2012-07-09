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
        return new Highlight_Test_Model_DbTable_Crawlable();
    }

    public function getCrawler()
    {
        return new Highlight_Test_Model_Crawler_Crawler();
    }

    public function getDataForTestCrawlerIsLookingInGivenField()
    {
        return array(
            array(
                array('title' => 'myTitle foo', 'description' => 'myDescription'),
                'title',
                'foo',
                true
            ),
            array(
                array('title' => 'myTitle', 'description' => 'myDescription'),
                'title',
                'bar',
                false
            ),
            array(
                array('title' => 'myTitle', 'description' => 'myDescription foo'),
                'description',
                'foo',
                true
            ),
            array(
                array('title' => 'myTitle', 'description' => 'myDescription'),
                'description',
                'foo',
                false
            ),
            array(
                array('title' => 'myTitle bar', 'description' => 'myDescription'),
                'title',
                'foo bar',
                true
            ),
        );
    }

    /**
     * @dataProvider getDataForTestCrawlerIsLookingInGivenField
     */
    public function testCrawlerIsLookingInGivenField($data, $field, $word, $mustFind)
    {
        $table = $this->getCrawlableTable();
        $crawler = $this->getCrawler();

        list($row, $created) = $table->getOrCreate($data);
        $result = $crawler->crawl(array(
            'fields'        => array($field),
            'terms'         => $word
        ));
        
        if(!$mustFind) {
            $this->assertNotInCollection($result, $row);
        }
        else {
            $this->assertInCollection($result, $row);
        }

        $row->delete();
    }


    public function assertInCollection(array $collection, Centurion_Db_Table_Row_Abstract $row)
    {
        foreach ($collection as $r) {
            if($r->id == $row->id) {
                if(get_class($row->getTable()) === get_class($r->getTable())) {
                    return true;
                }
            }
        }
        $this->fail('Could not find row in collection');
    }

    public function assertNotInCollection(array $collection, Centurion_Db_Table_Row_Abstract $row)
    {
        foreach ($collection as $r) {
            var_dump(gettype($r));
            if($r->id == $row->id) {
                if(get_class($row->getTable()) === get_class($r->getTable())) {
                    $this->fail('Found row in collection when none was expected');
                }
            }
        }

        return true;
    }

}

