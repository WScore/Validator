<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

class allTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s Validation' );
        $suite->addTestFile( __DIR__ . '/Validation_1_0/Rules_Test.php' );
        $suite->addTestFile( __DIR__ . '/Validation_1_0/Validate_Test.php' );
        return $suite;
    }
}
