<?php
namespace tests\Validation_1_0;

use WScore\Validation\Rules;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Rules_Test extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $rules = Rules::text();
        $this->assertEquals( 'WScore\Validation\Rules', get_class( $rules ) );
    }

    /**
     * @test
     */
    function text_required_sets_type_and_required()
    {
        $rules = Rules::text();
        $this->assertEquals( 'text', $rules['type'] );
        $this->assertEquals( 'text', $rules->getType() );
        $this->assertEquals( false, $rules['required'] );
        $this->assertEquals( false, $rules->isRequired() );
        $array = $rules->toArray();
        $this->assertEquals( 'text', $array['type'] );
        $this->assertEquals( false, $array['required'] );

        $rules = Rules::text()->required();
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
        $rules = Rules::integer( 'required|max:10' );
        $this->assertEquals( 'integer', $rules['type'] );
        $this->assertEquals( 'integer', $rules->getType() );
        $this->assertEquals( 10, $rules['max'] );
    }

    /**
     * @test
     */
    function mail_with_array_rules_sets_type_and_string()
    {
        $rules = Rules::mail( ['required','string'=>'lower'] );
        $this->assertEquals( 'mail', $rules['type'] );
        $this->assertEquals( 'lower', $rules['string'] );
    }

    /**
     * @test
     * @expectedException \BadMethodCallException
     */
    function invalid_type_throws_exception()
    {
        Rules::badType();
    }
}