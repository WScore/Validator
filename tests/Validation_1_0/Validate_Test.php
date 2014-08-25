<?php
namespace tests\Validation_1_0;

use WScore\Validation\Factory;
use WScore\Validation\Rules;
use WScore\Validation\Verify;
use WScore\Validation\Utils\ValueTO;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Validate_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Verify
     */
    public $validate;

    function setup()
    {
        $this->validate = Factory::buildValidate();
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Validation\Verify', get_class( $this->validate ) );
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
        $this->assertEquals( 'WScore\Validation\Verify', get_class( $this->validate ) );
        $this->assertEquals( false, $value->fails() );
        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( 'text', $value );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function result_returns_last_value()
    {
        $value = $this->validate->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertSame( $value, $this->validate->result() );
    }

    /**
     * @test
     */
    function apply_filter_message()
    {
        $value = $this->validate->applyFilters( 'text', [ 'message' => 'tested' ] );
        $this->assertEquals( 'tested', $value->message() );
    }

    /**
     * @test
     */
    function message_is_set_if_required_fails()
    {
        $value = $this->validate->applyFilters( '', [ 'required' => true ] );
        $this->assertEquals( true, $value->fails() );
        $this->assertEquals( 'required item', $value->message() );
    }

    /**
     * @test
     */
    function get_general_error_message()
    {
        $value = $this->validate->applyFilters( '', [] );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function get_message_based_type()
    {
        $value = $this->validate->applyFilters( '', ['type'=>'mail'] );
        $this->assertEquals( 'invalid mail format', $value->message() );
    }

    /**
     * @test
     */
    function match_message()
    {
        $value = $this->validate->applyFilters( '', ['matches'=>'number'] );
        $this->assertEquals( 'only numbers (0-9)', $value->message() );
        $value = $this->validate->applyFilters( '', ['matches'=>'int'] );
        $this->assertEquals( 'not an integer', $value->message() );
        $value = $this->validate->applyFilters( '', ['matches'=>'not-valid'] );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function locale_ja_returns_japanese_message()
    {
        $v = Factory::buildValidate('ja');

        $value = $v->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'WScore\Validation\Verify', get_class( $v ) );

        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( 'text', $value );
        $this->assertEquals( '入力内容を確認して下さい', $value->message() );

        // general message
        $value = $v->applyFilters( '', [] );
        $this->assertEquals( '入力内容を確認して下さい', $value->message() );

        // message based on method
        $value = $v->applyFilters( '', [ 'required' => true ] );
        $this->assertEquals( '必須項目です', $value->message() );

        // message based on type
        $value = $v->applyFilters( '', ['type'=>'mail'] );
        $this->assertEquals( 'メールアドレスが間違ってます', $value->message() );

        // message based on method/parameter
        $value = $v->applyFilters( '', ['matches'=>'number'] );
        $this->assertEquals( '数値のみです(0-9)', $value->message() );
        $value = $v->applyFilters( '', ['matches'=>'int'] );
        $this->assertEquals( '整数を入力してください', $value->message() );
        $value = $v->applyFilters( '', ['matches'=>'not-valid'] );
        $this->assertEquals( '入力内容を確認して下さい', $value->message() );
    }

    /**
     * @test
     */
    function is_for_array_input_lowers_character()
    {
        $input = [ 'abc', 'ABC', 'Abc' ];
        $found = $this->validate->is( $input, ['string'=>'lower'] );
        $this->assertFalse( $this->validate->result()->fails() );
        $this->assertEquals( 'abc', $found[0] );
        $this->assertEquals( 'abc', $found[1] );
        $this->assertEquals( 'abc', $found[2] );

        $found = $this->validate->result()->getValue();
        $this->assertEquals( 'abc', $found[0] );
        $this->assertEquals( 'abc', $found[1] );
        $this->assertEquals( 'abc', $found[2] );
    }

    /**
     * @test
     */
    function is_for_array_input_validates_bad_integer()
    {
        $input = [ '1', '2', 'bad', '3' ];

        $returned = $this->validate->is( $input, ['matches'=>'number'] );
        $this->assertTrue( $this->validate->result()->fails() );
        $this->assertFalse( $returned );

        $found   = $this->validate->result()->getValue();
        $this->assertEquals( '1', $found[0] );
        $this->assertEquals( '2', $found[1] );
        $this->assertEquals( 'bad', $found[2] );
        $this->assertEquals( '3', $found[3] );

        $messages = $this->validate->result()->message();
        $this->assertArrayNotHasKey( 0, $messages );
        $this->assertArrayNotHasKey( 1, $messages );
        $this->assertArrayNotHasKey( 3, $messages );
        $this->assertEquals( 'only numbers (0-9)', $messages[2] );
    }

    /**
     * @test
     */
    function closure_as_filter()
    {
        /**
         * @param ValueTO $v
         */
        $filter = function( $v ) {
            $val = $v->getValue();
            $val .= ':closure';
            $v->setValue( $val );
        };
        $found = $this->validate->is( 'test', Rules::text()->addCustom( 'my', $filter ) );
        $this->assertEquals( 'test:closure', $found );
    }

    /**
     * @test
     */
    function closure_with_error()
    {
        /**
         * @param ValueTO $v
         */
        $filter = function( $v ) {
            $val = $v->getValue();
            $val .= ':bad';
            $v->setValue( $val );
            $v->setError(__METHOD__);
            $v->setMessage('Closure with Error');
        };
        $found = $this->validate->is( 'test', Rules::text()->custom($filter) );
        $this->assertEquals( false, $found );
        /** @var ValueTO $valTo */
        $valTo = $this->validate->result();
        $this->assertTrue( $valTo->fails() );
        $this->assertTrue( $valTo->getBreak() );
        $this->assertEquals( 'test:bad', $valTo->getValue() );
        $this->assertEquals( 'Closure with Error', $valTo->message() );
    }
}
