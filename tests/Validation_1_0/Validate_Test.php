<?php
namespace tests\Validation_1_0;

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
    }

    /**
     * @test
     */
    function get_general_error_message()
    {
        $value = $this->validate->applyFilters( '', [] );
        $this->assertEquals( 'invalid input', $value->getMessage() );
    }

    /**
     * @test
     */
    function get_message_based_type()
    {
        $value = $this->validate->applyFilters( '', ['type'=>'mail'] );
        $this->assertEquals( 'invalid input', $value->getMessage() );
    }

    /**
     * @test
     */
    function match_message()
    {
        $value = $this->validate->applyFilters( '', ['matches'=>'number'] );
        $this->assertEquals( 'only numbers (0-9)', $value->getMessage() );
        $value = $this->validate->applyFilters( '', ['matches'=>'int'] );
        $this->assertEquals( 'not an integer', $value->getMessage() );
        $value = $this->validate->applyFilters( '', ['matches'=>'not-valid'] );
        $this->assertEquals( 'invalid input', $value->getMessage() );
    }

    /**
     * @test
     */
    function locale_ja_returns_japanese_message()
    {
        $v = Validate::getInstance('ja');

        $value = $v->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $v ) );

        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( 'text', $value );
        $this->assertEquals( '入力内容を確認して下さい', $value->getMessage() );

        // general message
        $value = $v->applyFilters( '', [] );
        $this->assertEquals( '入力内容を確認して下さい', $value->getMessage() );

        // message based on method
        $value = $v->applyFilters( '', [ 'required' => true ] );
        $this->assertEquals( '必須項目です', $value->getMessage() );

        // message based on type
        $value = $v->applyFilters( '', ['type'=>'mail'] );
        $this->assertEquals( '入力内容を確認して下さい', $value->getMessage() );

        // message based on method/parameter
        $value = $v->applyFilters( '', ['matches'=>'number'] );
        $this->assertEquals( '数値のみです(0-9)', $value->getMessage() );
        $value = $v->applyFilters( '', ['matches'=>'int'] );
        $this->assertEquals( '整数を入力してください', $value->getMessage() );
        $value = $v->applyFilters( '', ['matches'=>'not-valid'] );
        $this->assertEquals( '入力内容を確認して下さい', $value->getMessage() );
    }
}