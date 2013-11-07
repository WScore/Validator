<?php
namespace WStest02\Validation;

use WScore\Validation\Validate;
use WScore\Validation\ValueTO;
use WScore\Validation\Filter;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Validate_Test extends \PHPUnit_Framework_TestCase
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
    }

    /**
     * @test
     */
    function apply_filter_trim()
    {
        $value = $this->validate->applyFilters( ' text ', [ 'trim' => true ] );
        $this->assertEquals( 'WScore\Validation\Validate', get_class( $this->validate ) );
        $this->assertEquals( 'text', $value->getValue() );
    }

    /**
     * @test
     */
    function apply_filter_message()
    {
        $value = $this->validate->applyFilters( 'text', [ 'message' => 'tested' ] );
        $this->assertEquals( 'tested', $value->getMessage() );        
    }
}