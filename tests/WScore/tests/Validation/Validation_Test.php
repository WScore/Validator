<?php
namespace wsTests\Validation;
require_once( __DIR__ . '/../../autoloader.php' );

use \WScore\Core;

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validation */
    var $dio;
    
    /** @var \WScore\Validation\Rules */
    var $rule;
    
    public function setUp()
    {
        Core::go();
        $this->dio  = Core::get( '\WScore\Validation\Validation' );
        $this->rule = Core::get( '\WScore\Validation\Rules' );
    }
    public function getData()
    {
        return array(
            'email' => 'test@example.com',
            'number' => '123',
        );
    }
    // +----------------------------------------------------------------------+
    public function test_simple_invalid()
    {
        // todo: fix Validate
        $err_msg = 'Not a Number';
        $data = $this->getData();
        $this->dio->source( $data );
        $this->dio->push( 'email', $this->rule->number( 'message:'.$err_msg ) );
        $isError = $this->dio->isValid();
        $errors  = $this->dio->popError();
        $value   = $this->dio->pop();

        $this->assertEquals( $data[ 'email' ], $value[ 'email' ] );
        $this->assertFalse( $isError );
        $this->assertEquals( $err_msg, $errors['email'] );
    }
    public function test_validating_array()
    {
        // todo: fix Validate
        $input = array( 'num' => array( '1', '2', 'bad', '4' ) );
        $err_msg = 'Not a Number';
        $this->dio->source( $input );
        $rule = $this->rule->number( 'message:'.$err_msg );
        $this->dio->push( 'num', $rule );
        // check errors.
        $isError = $this->dio->isValid();
        $errors  = $this->dio->popError();
        
        $this->assertFalse( $isError );
        $this->assertNotEmpty( $err_msg, $errors['num'][2] );
        $this->assertEquals( $err_msg, $errors['num'][2] );

        // test popData. should have all the values
        $allData = $this->dio->pop();
        $this->assertTrue( isset( $allData['num'][2] ) );

        // test popSafe. should not have value with errors.
        $safeData = $this->dio->popSafe();
        $this->assertFalse( isset( $safeData['num'][2] ) );

    }
    public function test_simple_push_and_pop()
    {
        $data = $this->getData();
        $this->dio->source( $data );
        $this->dio->push( 'number', $this->rule->number() );
        $value = $this->dio->pop();

        $this->assertEquals( $data[ 'number' ], $value[ 'number' ] );
    }
    public function test_simple_push_mail()
    {
        $data = $this->getData();
        $this->dio->source( $data );
        $this->dio->push( 'email', $this->rule->email() );
        $value = $this->dio->pop();

        $this->assertEquals( $data[ 'email' ], $value[ 'email' ] );
    }
    public function test_multiple()
    {
        $input = array( 'date_y' => '1981', 'date_m' => '08', 'date_d' => '18' );
        $this->dio->source( $input );
        $this->dio->push( 'date', $this->rule->date() );
        
        $date = $this->dio->pop( 'date' );
        $this->assertEquals( '1981-08-18', $date );
        // check errors
    }
    // +----------------------------------------------------------------------+
}