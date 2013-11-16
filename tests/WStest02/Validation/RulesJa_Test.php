<?php
namespace WStest02\Validation;

use WScore\Validation\Rules;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class RulesJa_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Rules */
    var $rule;

    public function setUp()
    {
        $this->rule = new Rules( 'ja' );
    }
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $this->rule ) );
    }

    /**
     * @test
     */
    function hiragana_type_sets_mbConvert_as_hiragana()
    {
        $rule = $this->rule;
        $rule->applyType( 'hiragana' );
        $this->assertEquals( 'hiragana', $this->rule[ 'mbConvert' ] );
    }
}