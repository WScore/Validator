<?php
namespace WStest02\Validation;

use WScore\Validation\Validate;
use WScore\Validation\Validation;
use WScore\Validation\ValueTO;
use WScore\Validation\Filter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validation */
    var $validate;

    public function setUp()
    {
        $this->validate = new Validation(
            Validate::factory()
        );
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Validation', get_class( $this->validate ) );
    }
}