<?php
namespace wsTests\Validation;

//require_once( __DIR__ . '/../../autoloader.php' );
use \WScore\Validation\Validate;

class Validate_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validate */
    var $validate;

    public function setUp()
    {
        require_once( __DIR__ . '/../../../../scripts/require.php' );
        $this->validate = include( __DIR__ . '/../../../../scripts/validate.php' );
        // \Locale::setDefault( 'ja_JP' );
    }
    // +----------------------------------------------------------------------+
    function test_setting_message_override()
    {
        $text = 'text';
        $filters    = array( 'message' => 'not a number', 'matches' => 'number', );
        $ok = $this->validate->validate( $text, $filters, 'wow error!' );
        $this->assertFalse( $ok );
        $this->assertFalse( $this->validate->isValid );
        $error = $this->validate->err_msg;
        $this->assertEquals( 'wow error!', $error );
    }
    function test_setting_message()
    {
        $text = 'text';
        $filters    = array( 'message' => 'not a number', 'matches' => 'number', );
        $ok = $this->validate->validate( $text, $filters );
        $this->assertFalse( $ok );
        $this->assertFalse( $this->validate->isValid );
        $error = $this->validate->err_msg;
        $this->assertEquals( 'not a number', $error );
    }
    function test_setting_err_msg()
    {
        $text = 'text';
        $filters    = array( 'err_msg' => 'not a number', 'matches' => 'number', );
        $ok = $this->validate->validate( $text, $filters );
        $this->assertFalse( $ok );
        $this->assertFalse( $this->validate->isValid );
        $error = $this->validate->err_msg;
        $this->assertEquals( 'not a number', $error );
    }
    function test_missing_required_data()
    {
        $missing = '';
        $filters = array( 'required' => true );
        $ok = $this->validate->validate( $missing, $filters );
        $this->assertFalse( $ok );
        $this->assertFalse( $this->validate->isValid );
        $error = $this->validate->err_msg;
        $this->assertEquals( 'required item', $error );
    }
    function test_missing_required_array_data()
    {
        $missing = array( '1', '', '2' );
        $filters = array( 'required' => true );
        $ok = $this->validate->validate( $missing, $filters );
        $this->assertFalse( $ok );
        $this->assertFalse( $this->validate->isValid );
        $error = $this->validate->err_msg;
        $this->assertFalse( array_key_exists( 0, $error ) );
        $this->assertTrue(  array_key_exists( 1, $error ) );
        $this->assertFalse( array_key_exists( 2, $error ) );
        $this->assertEquals( 'required item', $error[1] );
    }
    function test_error_pattern_array()
    {
        $text = array( '1234', 'text', '5678' );
        $filters    = array( 'matches' => 'number' );
        $ok = $this->validate->validate( $text, $filters );
        $this->assertFalse( $ok );
        $this->assertFalse( $this->validate->isValid );
        $error = $this->validate->err_msg;
        $this->assertEquals( 'invalid matches with number', $error[1] );
    }
    function test_error_pattern_reports_option()
    {
        $text = 'text';
        $filters    = array( 'matches' => 'number' );
        $ok = $this->validate->validate( $text, $filters );
        $this->assertFalse( $ok );
        $this->assertFalse( $this->validate->isValid );
        $error = $this->validate->err_msg;
        $this->assertEquals( 'invalid matches with number', $error );
    }
    public function test_is_style()
    {
        // text with upper/lower cases. 
        $text_alpha = 'abcABC';
        $text = $text_alpha;

        // convert to lower case
        $filters = array( 'string' => 'lower' );
        $value = $this->validate->is( $text, $filters );
        $this->assertEquals( $text_alpha, $text );
        $this->assertEquals( strtolower( $text_alpha ), $value );
        $this->assertEquals( strtolower( $text_alpha ), $this->validate->value );

        // convert to upper case
        $filters = array( 'string' => 'upper' );
        $value = $this->validate->is( $text, $filters );
        $this->assertEquals( strtoupper( $text_alpha ), $value );
        $this->assertEquals( strtoupper( $text_alpha ), $this->validate->value );

        $text_number = '123490';
        $text = $text_number;
        $filters = array( 'matches' => 'number' );

        $value = $this->validate->is( $text, $filters );
        $this->assertEquals( $text_number, $value );
        $this->assertEquals( $text_number, $text );

        $text_alpha = 'text';
        $text = $text_alpha;

        $ok = $this->validate->is( $text, $filters );
        $this->assertFalse( $ok );
        $this->assertEquals( $text_alpha, $text );
    }
    public function test_basic_string()
    {
        // text with upper/lower cases. 
        $text_alpha = 'abcABC';
        $text = $text_alpha;

        // convert to lower case
        $filters = array( 'string' => 'lower' );
        $ok = $this->validate->validate( $text, $filters );
        $this->assertTrue( $ok );
        $this->assertEquals( $text_alpha, $text );
        $this->assertEquals( strtolower( $text_alpha ), $this->validate->value );

        // convert to upper case
        $filters = array( 'string' => 'upper' );
        $ok = $this->validate->validate( $text, $filters );
        $this->assertTrue( $ok );
        $this->assertEquals( strtoupper( $text_alpha ), $this->validate->value );
    }
    
    public function test_basic_pattern()
    {
        $text_number = '123490';
        $text = $text_number;
        $filters = array( 'matches' => 'number' );

        $ok = $this->validate->validate( $text, $filters );
        $this->assertTrue( $ok );
        $this->assertEquals( $text_number, $text );

        $text_alpha = 'text';
        $text = $text_alpha;

        $ok = $this->validate->validate( $text, $filters );
        $this->assertFalse( $ok );
        $this->assertEquals( $text_alpha, $text );
    }
}
