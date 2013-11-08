<?php
namespace WStest02\Validation;

use WScore\Validation\Validate;
use WScore\Validation\Rules;
use WScore\Validation\Filter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Validate_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validate */
    var $validate;

    /** @var  Rules */
    var $rules;

    public function setUp()
    {
        $this->validate = Validate::factory();
        $this->rules    = new Rules();
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
    }

    /**
     * @test
     */
    function apply_filter_trim()
    {
        $value = $this->validate->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
        $this->assertEquals( 'text', $value->getValue() );
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
    }

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
}