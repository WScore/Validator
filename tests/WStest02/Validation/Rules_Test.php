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

    /**
     * @test
     */
    function initial_rule_has_no_type_set()
    {
        $this->assertEquals( null, $this->rule->getType() );
    }

    /**
     * @test
     */
    function applyType_sets_type()
    {
        $type = 'text';
        $this->rule->applyType( $type );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $this->rule ) );
        $this->assertEquals( $type, $this->rule->getType() );
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    function applyType_badType_throws_runtimeException()
    {
        $type = 'bad type';
        $this->rule->applyType( $type );
    }

    /**
     * @test
     */
    function applying_type_sets_filters()
    {
        $rule = $this->rule;
        $rule->filter[ 'test_filter' ] = false;
        $rule->filterTypes[ 'test' ] = [ 'test_filter' => 'tested' ];

        // make sure test_filter is not set.
        $this->assertEquals( false, $rule[ 'test_filter'] );

        $type = 'test';
        $rule->applyType( $type );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( $type, $rule->getType() );
        $this->assertEquals( 'tested', $rule[ 'test_filter'] );
    }

    /**
     * @test
     */
    function set_required_filter()
    {
        $rule = $this->rule;
        $rule->applyType( 'text' );
        $this->assertEquals( false, $rule[ 'required' ] );
        $this->assertEquals( false, $rule->isRequired() );

        $rule->required();
        $this->assertEquals( true, $rule[ 'required' ] );
        $this->assertEquals( true, $rule->isRequired() );
    }
}