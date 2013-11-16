<?php

ini_set( 'display_errors', 1 );
error_reporting( E_ALL );

class All_Tests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s Validation' );
        $folder = __DIR__ . '/Validation/';
        $suite->addTestFile( $folder . 'Message_Test.php' );
        $suite->addTestFile( $folder . 'MessageJa_Test.php' );
        $suite->addTestFile( $folder . 'Rules_Test.php' );
        $suite->addTestFile( $folder . 'Validate_Test.php' );
        $suite->addTestFile( $folder . 'ValidateJa_Test.php' );
        $suite->addTestFile( $folder . 'Validation_Test.php' );
        $suite->addTestFile( $folder . 'ValidationJa_Test.php' );
        $suite->addTestFile( $folder . 'Filter_Test.php' );

        return $suite;
    }
}
