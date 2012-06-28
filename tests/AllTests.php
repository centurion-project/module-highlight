<?php

if (! defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Highlight_Test_AllTests::main');
}

require_once dirname(__FILE__) . '/../../../../tests/TestHelper.php';

class Highlight_Test_AllTests
{
    public static function main ()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite ()
    {
        $suite = new PHPUnit_Framework_TestSuite('Highlight test suite');
        $suite->addTest(Highlight_Test_Model_AllTests::suite());
        $suite->addTest(Highlight_Test_Crawler_AllTests::suite());
        return $suite;
    }
}
if (PHPUnit_MAIN_METHOD == 'Highlight_Test_AllTests::main') {
    Highlight_Test_AllTests::main();
}    
