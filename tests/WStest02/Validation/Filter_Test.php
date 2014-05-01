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
        $this->validate = Validate::getInstance();
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
    function mbConvert_for_hiragana_converts()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => 'hiragana' ] );
        $this->assertEquals( '012ABCあいうａｂｃかきくざじず', $value->getValue() );
    }

    /**
     * @test
     */
    function mbConvert_for_katakanau_converts()
    {
        $value = $this->validate->applyFilters( "012ABCあいうａｂｃカキクｻﾞｼﾞｽﾞ", [ 'mbConvert' => 'katakana' ] );
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

    /**
     * @test
     */
    function choice_works()
    {
        $choice = array( '1', '3' );
        $value = $this->validate->is( '1', array( 'choice' => $choice ) );
        $this->assertEquals( '1', $value );
        $this->assertEquals( true, $this->validate->isValid() );
    }

    /**
     * @test
     */
    function choice_fails()
    {
        $choice = array( '1', '3' );
        $value = $this->validate->is( '2', array( 'choice' => $choice ) );
        $this->assertEquals( false, $value );
        $this->assertEquals( false, $this->validate->isValid() );
        $this->assertEquals( 'invalid choice', $this->validate->getMessage() );
    }

    /**
     * @test
     */
    function kanaType_katakana_success_for_text_with_only_katakana()
    {
        $text = '　ァアイウエオヶ・ーヽヾ';
        $value = $this->validate->is( $text, array( 'kanaType' => 'katakana' ) );
        $this->assertEquals( $text, (string) $value );
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
    }

    /**
     * @test
     */
    function kanaType_katakana_fails_for_text_with_non_katakana()
    {
        // with hiragana
        $text = 'アイウエオ' . 'あ';
        $value = $this->validate->is( $text, array( 'kanaType' => 'katakana' ) );
        $this->assertEquals( false, $this->validate->isValid() );

        // with ascii
        $text = 'アイウエオ' . 'a';
        $value = $this->validate->is( $text, array( 'kanaType' => 'katakana' ) );
        $this->assertEquals( false, $this->validate->isValid() );

        // with space... not sure if this should fail
        $text = 'アイウエオ' . ' ';
        $value = $this->validate->is( $text, array( 'kanaType' => 'katakana' ) );
        $this->assertEquals( false, $this->validate->isValid() );
    }

    /**
     * @test
     */
    function kanaType_hiragana_success_for_text_with_only_hiragana()
    {
        $text = '　ぁあいうえおん゛ゞ';
        $value = $this->validate->is( $text, array( 'kanaType' => 'hiragana' ) );
        $this->assertEquals( $text, (string) $value );
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
    }

    /**
     * @test
     */
    function kanaType_hankana_success_for_text_with_only_hankaku_katakana()
    {
        $text = ' ｱﾝｧｨｩｪｫｬｭｮｯﾞﾞﾟ';
        $value = $this->validate->is( $text, array( 'kanaType' => 'hankana' ) );
        $this->assertEquals( $text, (string) $value );
        $this->assertEquals( true, $this->validate->isValid() );
        $this->assertEquals( null, $this->validate->getMessage() );
    }
}