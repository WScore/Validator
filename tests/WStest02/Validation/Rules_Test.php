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

    /**
     * @test
     */
    function static_parse_returns_new_rules()
    {
        $rule = Rules::parse( 'text' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule ) );
        $this->assertEquals( 'text', $rule->getType() );
    }

    /**
     * @test
     */
    function invoke()
    {
        $rule1 = $this->rule;
        /** @var Rules $rule2 */
        $rule2 = $rule1( 'text' );
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rule2 ) );
        $this->assertEquals( 'text', $rule2->getType() );

        // make sure that these are different.
        $this->assertNotEquals( $rule1, $rule2 );

        // apply the same type, and should be equal.
        $rule1->applyType( 'text' );
        $this->assertEquals( $rule1, $rule2 );

        // but not the same object.
        $this->assertNotSame( $rule1, $rule2 );
    }

    /**
     * @test
     */
    function apply_text_filter()
    {
        $filter = 'required|min:5|max:10';
        $this->rule->applyTextFilter( $filter );
        $this->assertEquals( true, $this->rule->isRequired() );
        $this->assertEquals(  5, $this->rule[ 'min' ] );
        $this->assertEquals( 10, $this->rule[ 'max' ] );
    }

    /**
     * @test
     */
    function apply_text_filter_on_invoke()
    {
        $filter = 'required|min:5|max:10|dummy:test';
        /** @var Rules $rule */
        $rule1 = $this->rule;
        $rule = $rule1( 'text', $filter );
        $this->assertEquals( true, $rule->isRequired() );
        $this->assertEquals(  5, $rule[ 'min' ] );
        $this->assertEquals( 10, $rule[ 'max' ] );
        $this->assertEquals( 'test', $rule[ 'dummy' ] );
    }

    /**
     * @test
     */
    function apply_text_filter_on_static_parse()
    {
        $filter = 'required|min:5|max:10|dummy:test';
        /** @var Rules $rule */
        $rule = Rules::parse( 'text', $filter );
        $this->assertEquals( true, $rule->isRequired() );
        $this->assertEquals(  5, $rule[ 'min' ] );
        $this->assertEquals( 10, $rule[ 'max' ] );
        $this->assertEquals( 'test', $rule[ 'dummy' ] );
    }

    /**
     * @test
     */
    function use_array_to_set_filter()
    {
        $this->assertEquals( false, $this->rule->getPattern() );
        $this->rule[ 'pattern' ] = 'some pattern';
        $this->assertEquals( 'some pattern', $this->rule->getPattern() );
    }

    /**
     * @test
     */
    function check_isset_on_false_filter()
    {
        $this->assertEquals( false, $this->rule->isRequired() );
        $this->assertEquals( true, isset( $this->rule[ 'required' ] ) );
    }

    /**
     * @test
     */
    function check_isset_on_non_existence_filter()
    {
        $this->assertEquals( false, isset( $this->rule[ 'test_test' ] ) );
        $this->rule[ 'test_test' ] = 'tested';
        $this->assertEquals( true, isset( $this->rule[ 'test_test' ] ) );
        $this->assertEquals( 'tested', $this->rule->getFilters( 'test_test' ) );
    }

    /**
     * @test
     */
    function get_filter_returns_all_filter_as_array()
    {
        $this->rule->min( 3 );
        $filters = $this->rule->getFilters();
        $this->assertEquals( true, is_array( $filters ) );
        $this->assertEquals( 3, $filters[ 'min' ] );
        $this->assertEquals( 3, $this->rule->getFilters( 'min' ) );
    }
}