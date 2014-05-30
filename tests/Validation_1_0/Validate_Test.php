<?php
namespace tests\Validation_1_0;

use WScore\Validation\Rules;
use WScore\Validation\Validate;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Validate_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Validate
     */
    public $validate;

    function setup()
    {
        $this->validate = Validate::getInstance();
    }

    function test0()
    {
        $rules = Rules::text();
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
    }

    // +----------------------------------------------------------------------+
    //  tests on applyFilter methods
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function apply_filter_trim()
    {
        $value = $this->validate->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( 'text', $value );
        $this->assertEquals( 'invalid input', $value->getMessage() );
    }

    /**
     * @test
     */
    function apply_filter_message()
    {
        $value = $this->validate->applyFilters( 'text', [ 'message' => 'tested' ] );
        $this->assertEquals( 'tested', $value->getMessage() );
    }

    /**
     * @test
     */
    function message_is_set_if_required_fails()
    {
        $value = $this->validate->applyFilters( '', [ 'required' => true ] );
        $this->assertEquals( 'required item', $value->getMessage() );
    }}