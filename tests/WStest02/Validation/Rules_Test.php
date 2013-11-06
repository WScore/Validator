<?php
namespace WStest02\Validation;

use WScore\Validation\Rules;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Rules_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Rules */
    var $rule;

    public function setUp()
    {
        $this->rule = new Rules();
    }
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $this->rule ) );
    }
}