<?php
namespace tests\Validation_1_0;

use WScore\Validation\Rules;
use WScore\Validation\ValidationFactory;
use WScore\Validation\Verify;
use WScore\Validation\Utils\ValueTO;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Verify_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Verify
     */
    public $verify;

    function setup()
    {
        $factory = new ValidationFactory();
        $this->verify = $factory->verify();
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Validation\Verify', get_class( $this->verify ) );
    }

    // +----------------------------------------------------------------------+
    //  tests on applyFilter methods
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function apply_filter_trim()
    {
        $value = $this->verify->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'WScore\Validation\Verify', get_class( $this->verify ) );
        $this->assertEquals( false, $value->fails() );
        $this->assertEquals( 'text', $value->getValue() );
        $this->assertEquals( 'text', $value );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function apply_filter_message()
    {
        $value = $this->verify->applyFilters( 'text', [ 'message' => 'tested' ] );
        $this->assertEquals( 'tested', $value->message() );
    }

    /**
     * @test
     */
    function message_is_set_if_required_fails()
    {
        $value = $this->verify->applyFilters( '', [ 'required' => true ] );
        $this->assertEquals( true, $value->fails() );
        $this->assertEquals( 'required item', $value->message() );
    }

    /**
     * @test
     */
    function get_general_error_message()
    {
        $value = $this->verify->applyFilters( '', [] );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function get_message_based_type()
    {
        $value = $this->verify->applyFilters( '', ['type'=>'mail'] );
        $this->assertEquals( 'invalid mail format', $value->message() );
    }

    /**
     * @test
     */
    function match_message()
    {
        $value = $this->verify->applyFilters( '', ['matches'=>'number'] );
        $this->assertEquals( 'only numbers (0-9)', $value->message() );
        $value = $this->verify->applyFilters( '', ['matches'=>'int'] );
        $this->assertEquals( 'not an integer', $value->message() );
        $value = $this->verify->applyFilters( '', ['matches'=>'not-valid'] );
        $this->assertEquals( 'invalid input', $value->message() );
    }

    /**
     * @test
     */
    function locale_ja_returns_japanese_message()
    {
        $factory = new ValidationFactory('ja');
        $v = $factory->verify();

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
        $found = $this->verify->is( $input, ['string'=>'lower'] );
        $this->assertEquals( 'abc', $found[0] );
        $this->assertEquals( 'abc', $found[1] );
        $this->assertEquals( 'abc', $found[2] );

        $valTO = $this->verify->apply( $input, ['string'=>'lower'] );
        $this->assertFalse( $valTO->fails() );
    }

    /**
     * @test
     */
    function is_for_array_input_validates_bad_integer()
    {
        $input = [ '1', '2', 'bad', '3' ];

        $returned = $this->verify->is( $input, ['matches'=>'number'] );
        $valTO    = $this->verify->apply( $input, ['matches'=>'number'] );
        $this->assertFalse( $returned );

        $found   = $valTO->getValue();
        $this->assertEquals( '1', $found[0] );
        $this->assertEquals( '2', $found[1] );
        $this->assertEquals( 'bad', $found[2] );
        $this->assertEquals( '3', $found[3] );

        $messages = $valTO->message();
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
        $found = $this->verify->is( 'test', Rules::text()->addCustom( 'my', $filter ) );
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
        $found = $this->verify->is( 'test', Rules::text()->custom($filter) );
        $this->assertEquals( false, $found );
        /** @var ValueTO $valTo */
        $valTo = $this->verify->apply( 'test', Rules::text()->custom($filter) );
        $this->assertTrue( $valTo->fails() );
        $this->assertTrue( $valTo->getBreak() );
        $this->assertEquals( 'test:bad', $valTo->getValue() );
        $this->assertEquals( 'Closure with Error', $valTo->message() );
    }
}
