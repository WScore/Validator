<?php
namespace WStest02\Validation;

use WScore\Validation\Validate;
use WScore\Validation\Rules;
use WScore\Validation\Filter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class ValidateJa_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validate */
    var $validate;

    /** @var  Rules */
    var $rules;

    /**
     * @var array
     */
    var $msg;

    public function setUp()
    {
        $this->validate = Validate::factory( 'ja' );
        $this->rules    = new Rules();
        $this->msg     = include( __DIR__ . '/../../../src/WScore/Validation/Locale/Lang.ja.php' );
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
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
        $this->assertEquals( $this->msg[0], $value->getMessage() );
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
        $this->assertEquals( $this->msg['required'], $value->getMessage() );
    }

    // +----------------------------------------------------------------------+
    //  tests on is methods
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function is_returns_filtered_value()
    {
        $value = $this->validate->is( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'text', $value );
    }

    /**
     * @test
     */
    function is_returns_false_if_filer_fails()
    {
        $value = $this->validate->is( '', [ 'required' => true ] );
        $this->assertEquals( false, $value );
    }

    // +----------------------------------------------------------------------+
    //  integrate test using Rules object
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function applyFilter_with_rules()
    {
        $rules = $this->rules;
        $value = $this->validate->applyFilters( ' text ', $rules( 'text' ) );
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( $this->msg[0], $value->getMessage() );
    }

    /**
     * @test
     */
    function is_with_rules()
    {
        $rules = $this->rules;
        $value = $this->validate->is( ' text ', $rules( 'text' ) );
        $this->assertEquals( 'text', $value );
    }

    /**
     * @test
     */
    function isValid_return_true_if_no_error()
    {
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
        $this->validate->is( 'text', array( 'trim' => true ) );
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
    }

    /**
     * @test
     */
    function getMessage_returns_msg_if_is_fails()
    {
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
        $this->validate->is( 'text', array( 'pattern' => '[0-9]*' ) );
        $this->assertEquals( false, $this->validate->isValid() );
        $this->assertEquals( $this->msg[0], $this->validate->getMessage() );
    }
}