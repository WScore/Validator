<?php
namespace WStest02\Validation;

use WScore\Validation\Rules;
use WScore\Validation\Validate;
use WScore\Validation\Validation;
use WScore\Validation\ValueTO;
use WScore\Validation\Filter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validation */
    var $validate;

    /** @var  Rules */
    var $rules;

    public function setUp()
    {
        $this->validate = new Validation(
            Validate::factory()
        );
        $this->rules = new Rules();
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Validation', get_class( $this->validate ) );
    }

    /**
     * @test
     */
    function find_a_single_value()
    {
        $rule = $this->rules;
        $source = array( 'test' => 'tested' );
        $this->validate->source( $source);
        $got = $this->validate->push( 'test', $rule('text') );

        $this->assertEquals( 'tested', $got );
        $this->assertEquals( 'tested', $this->validate->pop( 'test' ) );
        $this->assertEquals( $source, $this->validate->pop() );
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( array(), $this->validate->popError() );
    }

    /**
     * @test
     */
    function find_a_single_array_of_value()
    {
        $rule = $this->rules;
        $test = array( 'tested', 'more test' );
        $source = array( 'test' => $test );
        $this->validate->source( $source );
        $got = $this->validate->push( 'test', $rule('text') );

        $this->assertEquals( $test, $got );
        $this->assertEquals( $test, $this->validate->pop( 'test' ) );
        $this->assertEquals( $source, $this->validate->pop() );
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( array(), $this->validate->popError() );
    }

    /**
     * @test
     */
    function find_will_result_inValid_if_required_data_is_not_set()
    {
        $rule = $this->rules;
        $source = array( 'test' => '' );
        $this->validate->source( $source );
        $got = $this->validate->push( 'test', $rule('text')->required() );

        $this->assertEquals( '', $got );
        $this->assertEquals( '', $this->validate->pop( 'test' ) );
        $this->assertEquals( $source, $this->validate->pop() );
        $this->assertEquals( array(), $this->validate->popSafe() );
        $this->assertEquals( false, $this->validate->isValid() );
        $errors =  $this->validate->popError();
        $this->assertEquals( 'required item', $errors[ 'test' ] );
    }

    /**
     * @test
     */
    function find_will_return_array_of_errors_if_input_is_an_array()
    {
        $rule = $this->rules;
        $test = array( '123', 'more test', '456' );
        $source = array( 'test' => $test );
        $collect = array( 'test' => array( 0=>'123', 2=>'456') );
        $this->validate->source( $source );
        $got = $this->validate->push( 'test', $rule('number') );

        // should return the input
        $this->assertEquals( false, $got );
        $this->assertEquals( $test, $this->validate->pop( 'test' ) );
        $this->assertEquals( $source, $this->validate->pop() );

        // validation should become inValid.
        $this->assertEquals( false, $this->validate->isValid() );
        $errors =  $this->validate->popError();
        $this->assertEquals( 'invalid input', $errors[ 'test' ][1] );

        // popSafe returns data without error value.
        $this->assertEquals( $collect, $this->validate->popSafe() );
    }
}