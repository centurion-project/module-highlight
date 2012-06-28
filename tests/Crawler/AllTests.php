<?php

if (! defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Highlight_Test_Crawlers_AllTests::main');
}

require_once dirname(__FILE__) . '/../../../../../tests/TestHelper.php';

class Highlight_Test_Crawler_AllTests
{

    public static function main ()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite ()
    {
        $suite = new PHPUnit_Framework_TestSuite('Highlight Crawler test suite Suite');
        $suite->addTestSuite('Highlight_Test_Crawler_CrawlerTest');

        return $suite;
    }
}
if (PHPUnit_MAIN_METHOD == 'Highlight_Test_Crawler_AllTests::main') {
    Highlight_Test_Crawlers_AllTests::main();
}    
