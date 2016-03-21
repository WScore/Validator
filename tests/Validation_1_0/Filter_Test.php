<?php
namespace tests\Validation_1_0;

use WScore\Validation\Rules;
use WScore\Validation\Utils\ValueTO;
use WScore\Validation\ValidationFactory;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Filter_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \WScore\Validation\Verify
     */
    var $validate;

    public function setUp()
    {
        $factory = new ValidationFactory();
        $this->validate = $factory->verify();
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Verify', get_class( $this->validate ) );
        $value = $this->validate->applyFilters( 'test' );
        $this->assertEquals( 'WScore\Validation\Utils\ValueTO', get_class( $value ) );
        $this->assertEquals( 'test', (string) $value );
    }

    /**
     * @test
     */
    function err_msg_sets_message()
    {
        $value = $this->validate->applyFilters( 'test', [ 'err_msg' => 'err-msg' ] );
        $this->assertEquals( 'err-msg', $value->message() );
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
        $this->assertTrue( !!$value->message() );
    }

    /**
     * @test
     */
    function encoding_fails()
    {
        $bad = 'bad' . chr( 11111111 );
        $value = $this->validate->applyFilters( $bad, [ 'encoding' => true ] );
        $this->assertEquals( '', $value->getValue() );
        $this->assertEquals( 'invalid encoding', $value->message() );
        $this->assertTrue( !!$value->message() );
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
        $value = $this->validate->applyFilters( "012abcあいうカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => Rules::MB_HAN_KANA ] );
        $this->assertEquals( '012abcｱｲｳｶｷｸｻﾞｼﾞｽﾞ', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_hankaku_converts_ascii()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => Rules::MB_HANKAKU ] );
        $this->assertEquals( '012ABCあいうabcカキクザジズ', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_zenkaku_converts_ascii()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => Rules::MB_ZENKAKU ] );
        $this->assertEquals( '０１２ＡＢＣあいうａｂｃカキクザジズ', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_hiragana_converts()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => Rules::MB_HIRAGANA ] );
        $this->assertEquals( '012ABCあいうａｂｃかきくざじず', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_katakanau_converts()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => Rules::MB_KATAKANA ] );
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
        /**
         * @param ValueTO $v
         * @return string
         */
        $closure = function( $v ) {
            $val = $v->getValue();
            $v->setValue( $val . ':closured' );
        };
        $value = $this->validate->applyFilters( 'test', [ 'some' => $closure ] );
        $this->assertEquals( 'test:closured', $value->getValue() );
    }

    /**
     * @test
     */
    function choice_works()
    {
        $choice = array( '1', '3' );
        $value = $this->validate->apply( '1', array( 'in' => $choice ) );
        $this->assertEquals( '1', $value->getValue() );
        $this->assertEquals( false, $value->fails() );
    }

    /**
     * @test
     */
    function choice_fails()
    {
        $choice = array( '1', '3' );
        $value = $this->validate->apply( '2', array( 'in' => $choice ) );
        $this->assertEquals( true, $value->fails() );
        $this->assertEquals( '2', $value->getValue() );
        $this->assertEquals( true, $value->fails() );
        $this->assertEquals( 'invalid choice', $value->message() );
    }

    /**
     * @test
     */
    function kanaType_katakana_success_for_text_with_only_katakana()
    {
        $text = '　ァアイウエオヶ・ーヽヾ';
        $value = $this->validate->is( $text, array( 'kanaType' => Rules::ONLY_KATAKANA ) );
        $this->assertEquals( $text, (string) $value );
    }

    /**
     * @test
     */
    function kanaType_katakana_fails_for_text_with_non_katakana()
    {
        // with hiragana
        $text = 'アイウエオ' . 'あ';
        $value = $this->validate->apply( $text, array( 'kanaType' => Rules::ONLY_KATAKANA ) );
        $this->assertEquals( true, $value->fails() );
//        $this->assertEquals( null, $this->validate->result()->message() );

        // with ascii
        $text = 'アイウエオ' . 'a';
        $value = $this->validate->apply( $text, array( 'kanaType' => Rules::ONLY_KATAKANA ) );
        $this->assertEquals( true, $value->fails() );
//        $this->assertEquals( null, $this->validate->result()->message() );

        // with space... not sure if this should fail
        $text = 'アイウエオ' . ' ';
        $value = $this->validate->apply( $text, array( 'kanaType' => Rules::ONLY_KATAKANA ) );
        $this->assertEquals( true, $value->fails() );
//        $this->assertEquals( null, $this->validate->result()->message() );
    }

    /**
     * @test
     */
    function kanaType_hiragana_success_for_text_with_only_hiragana()
    {
        $text = '　ぁあいうえおん゛ゞ';
        $value = $this->validate->is( $text, array( 'kanaType' => Rules::ONLY_HIRAGANA ) );
        $this->assertEquals( $text, (string) $value );
    }

    /**
     * @test
     */
    function kanaType_hankana_success_for_text_with_only_hankaku_katakana()
    {
        $text = ' ｱﾝｧｨｩｪｫｬｭｮｯﾞﾞﾟ';
        $value = $this->validate->is( $text, array( 'kanaType' => Rules::ONLY_HANKAKU_KANA ) );
        $this->assertEquals( $text, (string) $value );
    }

    /**
     * @test
     */
    function datetime_filter_removes_value_for_invalid_datetime_string()
    {
        $bad = '1234567890123456789012345678901234567890';
        $value = $this->validate->is( $bad, array( 'datetime'=>true));
        $this->assertEquals( '', (string) $value );
    }
}