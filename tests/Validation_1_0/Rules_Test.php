<?php
namespace tests\Validation_1_0;

use WScore\Validation\Rules;
use WScore\Validation\ValidationFactory;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Rules_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidationFactory
     */
    private $factory;

    function setup()
    {
        $this->factory = new ValidationFactory();
    }
    function test0()
    {
        $rules = $this->factory->rules();
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rules ) );
    }

    /**
     * @test
     */
    function text_required_sets_type_and_required()
    {
        $rules = $this->factory->rules()->withType('text');
        $this->assertEquals( 'text', $rules['type'] );
        $this->assertEquals( 'text', $rules->getType() );
        $this->assertEquals( false, $rules['required'] );
        $this->assertEquals( false, $rules->isRequired() );
        $array = $rules->toArray();
        $this->assertEquals( 'text', $array['type'] );
        $this->assertEquals( false, $array['required'] );

        /** @var Rules $rules */
        $rules = $rules->withType('text')->required();
        $this->assertEquals( 'text', $rules['type'] );
        $this->assertEquals( true, $rules['required'] );
        $this->assertEquals( true, $rules->isRequired() );
        $array = $rules->toArray();
        $this->assertEquals( 'text', $array['type'] );
        $this->assertEquals( true, $array['required'] );
    }

    /**
     * @test
     */
    function int_with_rules_sets_type_and_max()
    {
        $rules = $this->factory->rules()->withType('integer'); 
        $rules->apply( 'required|max:10' );
        $this->assertEquals( 'integer', $rules['type'] );
        $this->assertEquals( 'integer', $rules->getType() );
        $this->assertEquals( 10, $rules['max'] );
    }

    /**
     * @test
     */
    function mail_with_array_rules_sets_type_and_string()
    {
        $rules = $this->factory->rules()->withType('mail');
        $rules->apply( ['required','string'=>'lower'] );
        $this->assertEquals( 'mail', $rules['type'] );
        $this->assertEquals( 'lower', $rules['string'] );
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    function invalid_type_throws_BadMethodCallException()
    {
        $this->factory->rules()->withType('bad');
    }

    /**
     * @test
     */
    function object__is_equivalent_as_static_text_required()
    {
        $rules = new Rules();
        $rules = $rules->applyType('text');
        $this->assertEquals( 'text', $rules['type'] );
        $this->assertEquals( 'text', $rules->getType() );
        $this->assertEquals( false, $rules['required'] );
        $this->assertEquals( false, $rules->isRequired() );
        $array = $rules->toArray();
        $this->assertEquals( 'text', $array['type'] );
        $this->assertEquals( false, $array['required'] );

        /** @var Rules $rules */
        $rules = $rules->applyType('text')->required();
        $this->assertEquals( 'text', $rules['type'] );
        $this->assertEquals( true, $rules['required'] );
        $this->assertEquals( true, $rules->isRequired() );
        $array = $rules->toArray();
        $this->assertEquals( 'text', $array['type'] );
        $this->assertEquals( true, $array['required'] );
    }

    /**
     * @test
     */
    function locale_ja_loads_filters_and_types_in_ja()
    {
        $rules = $this->factory->rules()->withType('text');
        $this->assertEquals( 'text', $rules->getType() );
        $this->assertEquals( null, $rules['mbConvert'] );
        
        $rules = (new ValidationFactory('ja'))->rules()->withType('text');
        $this->assertEquals( 'text', $rules->getType() );
        $this->assertEquals( 'standard', $rules['mbConvert'] );
    }

    /**
     * @test
     */
    function test2()
    {
        $rule0 = $this->factory->rules()->withType('text');
        $rule1 = $this->factory->rules()->withType('text');
        $this->assertEquals( $rule0, $rule1 );
        $this->assertNotSame( $rule0, $rule1 );
    }
}
