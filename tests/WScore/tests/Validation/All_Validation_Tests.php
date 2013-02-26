<?php

    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );

class Validation_SuiteTests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s Validation' );
        $folder = __DIR__ . '/';
        $suite->addTestFile( $folder . 'Validate_test.php' );
        $suite->addTestFile( $folder . 'Rules_Test.php' );
        $suite->addTestFile( $folder . 'Validation_Test.php' );

        return $suite;
    }
}
