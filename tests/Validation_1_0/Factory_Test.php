<?php
namespace tests\Validation_1_0;

use WScore\Validation\Factory;
use WScore\Validation\Rules;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Factory_Test extends \PHPUnit_Framework_TestCase
{
    function setup()
    {
        Factory::setLocale( 'en', null );
    }
    
    function tearDown()
    {
        Factory::setLocale( 'en', null );
    }

    function test_default_locale_is_en()
    {
        $this->assertEquals( 'en', Factory::$locale );
    }
    
    function test_setting_locale()
    {
        Factory::setLocale( 'ja' );
        $this->assertEquals( 'ja', Factory::getLocale() );
    }
    
    function test_message_default_loads_english_texts()
    {
        $message = Factory::buildMessage();
        $this->assertEquals( 'WScore\Validation\Message', get_class( $message ) );
        $this->assertEquals( 'invalid input', $message->find('no',null, null) );
    }
    
    function test_message_with_locale_ja_loads_japanese_texts()
    {
        Factory::setLocale( 'ja' );
        $message = Factory::buildMessage();
        $this->assertEquals( 'WScore\Validation\Message', get_class( $message ) );
        $this->assertEquals( '入力内容を確認して下さい', $message->find('no',null, null) );
    }
    
    function test_rules_with_default_locale()
    {
        $rules = Rules::getInstance();
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rules ) );
        $this->assertEquals( null, $rules['mbConvert'] );
    }

    function test_rules_with_Japanese_locale()
    {
        Factory::setLocale( 'ja' );
        $rules = Rules::getInstance();
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rules ) );
        $this->assertEquals( 'standard', $rules['mbConvert'] );
    }

    function test_validation_builder()
    {
        $v = Factory::buildValidation();
        $this->assertEquals( 'WScore\Validation\Validation', get_class( $v ) );
    }
    
    function test_rules_builder()
    {
        $r = Factory::buildRules();
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $r ) );
    }
}