<?php

require_once dirname(__FILE__) . '/../../../../../tests/TestHelper.php';

class Highlight_Test_Model_ContainerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        
    }
    
    protected function tearDown()
    {
        $table = self::getTable();
        foreach ($table->fetchAll() as $row) {
            $row->delete();
        }
    }

    public static function getTable()
    {
        return Centurion_Db::getSingleton('highlight/container');
    }


    /** 
     * Tests that method createWithName works as expected.
     */
    public function testCreateContainerWithName()
    {
        $table = self::getTable();

        $c1 = $table->createWithName('bidule');
        $this->assertNotNull($c1, 'No instance returned after creation');
        $this->assertEquals('bidule', $c1->name, 'container created with different name');

        $c2 = null;
        try {
            $c2 = $table->createWithName('bidule');
            $this->fail('Could create a container with same name');
        }
        catch(InvalidArgumentException $e) {
        }

        try {
            $c2 = $table->createWithName(array('bidule'));
            $this->fail('Could create container with something else than a string');
        }
        catch(InvalidArgumentException $e ) {
        }
        $c1->delete();
    }

    public function testCreateContainerWithProxy()
    {
        $table = self::getTable();
        $simpleTable = Centurion_Db::getSingleton('asset/simple');
        list($row,) = $simpleTable->getOrCreate(array('title'=>'testCreateContainerWithProxy'));

        $c1 = $table->createWithProxy($row);
        $this->assertNotNull($c1, 'Could not create container from proxy');
        $this->assertEquals($c1->getProxy()->getTableClass(), $row->getTableClass(), 'attached proxy is of a different type');
        $this->assertEquals($c1->getProxy()->id, $row->id, 'attached proxy is different from created row');

        try {
            $c2 = $table->createWithProxy($row);
            $this->fail('Could create a container with same proxy');
        }
        catch(InvalidArgumentException $e) {
        }
        
        $c1->delete();
    }
}

