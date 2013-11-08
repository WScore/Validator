<?php
namespace WStest02\Validation;

use WScore\Validation\Rules;
use WScore\Validation\Validate;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Filter_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validate */
    var $validate;

    public function setUp()
    {
        $this->validate = Validate::factory();
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
        $value = $this->validate->applyFilters( 'test' );
        $this->assertEquals( 'WScore\Validation\ValueTO', get_class( $value ) );
        $this->assertEquals( 'test', (string) $value );
    }

    /**
     * @test
     */
    function err_msg_sets_message()
    {
        $value = $this->validate->applyFilters( 'test', [ 'err_msg' => 'err-msg' ] );
        $this->assertEquals( 'err-msg', $value->getMessage() );
    }

    /**
     * @test
     */
    function default_sets_value()
    {
        $value = $this->validate->applyFilters( '', [ 'default' => 'tested' ] );
        $this->assertEquals( 'tested', $value->getValue() );
    }

    /**
     * @test
     */
    function noNull_removes_null()
    {
        $value = $this->validate->applyFilters( "my\0Test", [ 'noNull' => true ] );
        $this->assertEquals( 'myTest', $value->getValue() );
    }

    /**
     * @test
     */
    function string_to_lower()
    {
        $value = $this->validate->applyFilters( 'test TEST', [ 'string' => 'lower' ] );
        $this->assertEquals( 'test test', $value->getValue() );
    }

    /**
     * @test
     */
    function string_to_upper()
    {
        $value = $this->validate->applyFilters( 'test TEST', [ 'string' => 'upper' ] );
        $this->assertEquals( 'TEST TEST', $value->getValue() );
    }

    /**
     * @test
     */
    function string_to_capital()
    {
        $value = $this->validate->applyFilters( 'test test', [ 'string' => 'capital' ] );
        $this->assertEquals( 'Test Test', $value->getValue() );
    }

    /**
     * @test
     */
    function pattern_fails()
    {
        $value = $this->validate->applyFilters( '3', [ 'pattern' => '[012]{1}' ] );
        $this->assertEquals( '3', $value->getValue() );
        $this->assertTrue( !!$value->getError() );
    }

    /**
     * @test
     */
    function encoding_fails()
    {
        $bad = 'bad' . chr( 11111111 );
        $value = $this->validate->applyFilters( $bad, [ 'encoding' => true ] );
        $this->assertEquals( '', $value->getValue() );
        $this->assertEquals( 'invalid encoding', $value->getMessage() );
        $this->assertTrue( !!$value->getError() );
    }
    // +----------------------------------------------------------------------+
    //  test mbConvert filter.
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function mbConvert_for_general_converts_hankakuKana_to_zenkakuKana()
    {
        $value = $this->validate->applyFilters( "012abcあいうカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => true ] );
        $this->assertEquals( '012abcあいうカキクザジズ', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_han_kana_converts_hankakuKana()
    {
        $value = $this->validate->applyFilters( "012abcあいうカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => 'han_kana' ] );
        $this->assertEquals( '012abcｱｲｳｶｷｸｻﾞｼﾞｽﾞ', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_hankaku_converts_ascii()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => 'hankaku' ] );
        $this->assertEquals( '012ABCあいうabcカキクザジズ', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_zenkaku_converts_ascii()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => 'zenkaku' ] );
        $this->assertEquals( '０１２ＡＢＣあいうａｂｃカキクザジズ', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_zen_hira_converts()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => 'zen_hira' ] );
        $this->assertEquals( '012ABCあいうａｂｃかきくざじず', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_zen_kanau_converts()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => 'zen_kana' ] );
        $this->assertEquals( '012ABCアイウａｂｃカキクザジズ', $value->getValue() );
    }
    // +----------------------------------------------------------------------+
    //  test closure
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function closure_works()
    {
        $closure = function( $v ) {
            return $v . ':closured';
        };
        $value = $this->validate->applyFilters( 'test', [ 'some' => $closure ] );
        $this->assertEquals( 'test:closured', $value->getValue() );
    }
}