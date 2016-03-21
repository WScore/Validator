<?php
namespace tests\Validation_1_0;

use WScore\Validation\Dio;
use WScore\Validation\ValidationFactory;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Dio_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dio
     */
    public $validate;

    /**
     * @var ValidationFactory
     */
    private $factory;

    function setup()
    {
        $this->make();
    }
    
    function make($locale='en') {
        $this->factory = new ValidationFactory($locale);
        $this->validate = $this->factory->on();
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Validation\Dio', get_class( $this->validate ) );
    }

    /**
     * @test
     */
    function Dio_normal_usage()
    {
        $source = array( 'test' => 'tested', 'more' => 'done' );
        $input  = $this->factory->on($source);
        $input->asText('test');
        $input->asText('more');
        $this->assertEquals(false, $input->fails());
        $this->assertEquals(true,  $input->passes());
        $this->assertEquals($source,  $input->get());
        
        $input->asText('bad')->required();
        $this->assertEquals(true, $input->fails());
        $this->assertEquals(false,  $input->passes());
        $source['bad'] = null;
        $this->assertEquals($source,  $input->get());
        $this->assertEquals(['bad'=>'required item'],  $input->message());
    }

    /**
     * @test
     */
    function is_validates_and_returns_the_value()
    {
        $source = array( 'test' => 'tested' );
        $this->validate->source( $source);
        $got = $this->validate->is( 'test', $this->validate->getRule('text') );

        $this->assertEquals( 'tested', $got );
        $this->assertEquals( 'tested', $this->validate->get( 'test' ) );
        $this->assertEquals( $source, $this->validate->get() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->message() );
    }

    /**
     * @test
     */
    function find_will_result_inValid_if_required_data_is_not_set()
    {
        $source = array( 'test' => '' );
        $this->validate->source( $source );
        $got = $this->validate->is( 'test', $this->validate->getRule('text')->required() );

        $this->assertEquals( '', $got );
        $this->assertEquals( '', $this->validate->get( 'test' ) );
        $this->assertEquals( $source, $this->validate->get() );
        $this->assertEquals( array(), $this->validate->getSafe() );
        $this->assertEquals( true, $this->validate->fails() );
        $errors =  $this->validate->message();
        $this->assertEquals( 'required item', $errors[ 'test' ] );
    }

    /**
     * @test
     */
    function verify_validates_a_value()
    {
        $rule = $this->factory->rules();
        $this->assertEquals( 'test', $this->validate->verify( 'test', $rule->withType('text') ) );
        $this->assertEquals( 'test', $this->validate->verify( 'TEST', $rule->withType('text')->string('lower') ) );
        $this->assertEquals( false,  $this->validate->verify( 'b@d',  $rule->withType('text')->pattern('[a-z]*') ) );
    }
    // +----------------------------------------------------------------------+
    //  test for array input
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function is_validates_and_returns_an_array_as_input()
    {
        $test = array( 'tested', 'more test' );
        $source = array( 'test' => $test );
        $this->validate->source( $source );
        $this->validate->asText( 'test' );
        $got = $this->validate->get('test');

        $this->assertEquals( $test, $got );
        $this->assertEquals( $test, $this->validate->get( 'test' ) );
        $this->assertEquals( $source, $this->validate->get() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->message() );
    }

    /**
     * @test
     */
    function is_return_array_of_errors_if_input_is_an_array()
    {
        $test = array( '123', 'more test', '456' );
        $source = array( 'test' => $test );
        $collect = array( 'test' => array( 0=>'123', 2=>'456') );
        $this->validate->source( $source );
        $this->validate->asNumber( 'test' );
        $got = $this->validate->get('test');

        // should return the input
        $this->assertEquals( ['123', 2=>'456'], $got );
        $this->assertEquals( $test, $this->validate->get( 'test' ) );
        $this->assertEquals( $source, $this->validate->get() );

        // validation should become inValid.
        $this->assertEquals( true, $this->validate->fails() );
        $errors =  $this->validate->message();
        $this->assertEquals( 'only numbers (0-9)', $errors[ 'test' ][1] );

        // popSafe returns data without error value.
        $this->assertEquals( $collect, $this->validate->getSafe() );
    }

    // +----------------------------------------------------------------------+
    //  test for multiple input
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function find_multiple_date_value()
    {
        $source = array( 'test_y'=>'2013', 'test_m'=>'11', 'test_d'=>'08' );
        $this->validate->source( $source );
        $this->validate->asDate( 'test' );
        $got = $this->validate->get('test');

        $this->assertEquals( '2013-11-08', $got );
        $this->assertEquals( '2013-11-08', $this->validate->get( 'test' ) );
        $this->assertEquals( array( 'test' => '2013-11-08'), $this->validate->get() );
        $this->assertEquals( true, $this->validate->passes() );
        $this->assertEquals( array(), $this->validate->message() );
    }

    /**
     * @test
     */
    function find_multiple_datetime_value()
    {
        $source = array( 'test_y'=>'2013', 'test_m'=>'11', 'test_d'=>'08', 'test_h'=>'15', 'test_i'=>'13', 'test_s'=>'59' );
        $this->validate->source( $source );
        $this->validate->asDateTime( 'test' );
        $got = $this->validate->get('test');

        $this->assertEquals( '2013-11-08 15:13:59', $got );
        $this->assertEquals( '2013-11-08 15:13:59', $this->validate->get( 'test' ) );
        $this->assertEquals( array( 'test' => '2013-11-08 15:13:59'), $this->validate->get() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->message() );
    }

    // +----------------------------------------------------------------------+
    //  test for sameWith rule
    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function is_finds_mail()
    {
        $source = array(
            'mail1' => 'Email@Example.com'
        );
        $mail = 'email@example.com';
        $this->validate->source( $source );
        $this->validate->asMail( 'mail1' );
        $got = $this->validate->get('mail1');

        $this->assertEquals( 'email@example.com', $got );
        $this->assertEquals( 'email@example.com', $this->validate->get( 'mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->get() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->message() );
    }
    
    /**
     * @test
     */
    function sameWith_checks_for_another_input()
    {
        $source = array(
            'mail1' => 'Email@Example.com',
            'mail2' => 'EMAIL@example.com'
        );
        $mail = 'email@example.com';
        $this->validate->source( $source );
        $this->validate->asMail( 'mail1' )->sameWith( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( 'email@example.com', $got );
        $this->assertEquals( 'email@example.com', $this->validate->get( 'mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->get() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->message() );
    }

    /**
     * @test
     */
    function sameWith_fails_if_nothing_to_compare()
    {
        $source = array(
            'mail1' => 'Email@Example.com',
        );
        $mail = 'email@example.com';
        $this->validate->source( $source );
        $this->validate->asMail( 'mail1' )->sameWith( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( false, $got );
        $this->assertEquals( $mail, $this->validate->get( 'mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->get() );
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( array( 'mail1'=>'missing value to compare' ), $this->validate->message() );
    }

    /**
     * @test
     */
    function sameWith_fails_if_not_the_same()
    {
        $source = array(
            'mail1' => 'Email@Example.com',
            'mail2' => 'Email2@Example.com',
        );
        $mail = 'email@example.com';
        $this->validate->source( $source );
        $this->validate->asMail( 'mail1' )->sameWith( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( false, $got );
        $this->assertEquals( $mail, $this->validate->get( 'mail1' ) );
        $this->assertEquals( array( 'mail1'=>$mail ), $this->validate->get() );
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( array( 'mail1'=>'value not the same' ), $this->validate->message() );
    }

    /**
     * @test
     */
    function sameWith_NOT_fails_if_input_is_missing()
    {
        $source = array(
            'mail1' => '',
            'mail2' => 'Email2@Example.com',
        );
        $this->validate->source( $source );
        $this->validate->asMail( 'mail1' )->sameWith( 'mail2');
        $got = $this->validate->get('mail1');

        $this->assertEquals( '', $got );
        $this->assertEquals( '', $this->validate->get( 'mail1' ) );
        $this->assertEquals( array( 'mail1'=>'' ), $this->validate->get() );
        $this->assertEquals( false, $this->validate->fails() );
        $this->assertEquals( array(), $this->validate->message() );
    }

    /**
     * @test
     */
    function sameWith_fails_on_required_item()
    {
        $source = array(
            'mail1' => '',
            'mail2' => 'Email2@Example.com',
        );
        $this->validate->source( $source );
        $this->validate->asMail( 'mail1' )->sameWith( 'mail2')->required();
        $got = $this->validate->get('mail1');

        $this->assertEquals( '', $got );
        $this->assertEquals( '', $this->validate->get( 'mail1' ) );
        $this->assertEquals( array( 'mail1'=>'' ), $this->validate->get() );
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( array( 'mail1'=>'required item' ), $this->validate->message() );
    }

    /**
     * @test
     */
    function multiple_input()
    {
        $input = [ 'a_y1'=>'2014', 'a_m1'=>'05', 'a_d1'=>'01', 'a_y2'=>'2014', 'a_m2'=>'07' ];
        $this->validate->source($input);
        $this->validate->asText( 'a' )->multiple( [
            'suffix' => 'y1,m1,y2,m2',
            'format' => '%04d/%02d - %04d/%02d'
        ] )->required();
        $found = $this->validate->get('a');
        $this->assertEquals( '2014/05 - 2014/07', $found );
    }

    /**
     * @test
     */
    function requiredIf_simple_case()
    {
        $input = [
            'flag' => 'a',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->asText('flag');
        $this->validate->asText('done')->requiredIf('flag');
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( 'required item', $this->validate->message('done') );
    }

    /**
     * @test
     */
    function requiredIf_with_possible_values()
    {
        $input = [
            'flag' => 'a',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->asText('flag');
        $this->validate->asText('done')->requiredIf('flag', ['a']);
        $this->assertEquals( true, $this->validate->fails() );
        $this->assertEquals( 'required item', $this->validate->message('done') );
    }

    /**
     * @test
     */
    function requiredIf_without_possible_values()
    {
        $input = [
            'flag' => 'a',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->asText('flag');
        $this->validate->asText('done')->requiredIf('flag', ['b']);
        $this->assertEquals( true, $this->validate->passes() );
    }

    /**
     * @test
     */
    function requiredIf_after_rule_is_applyed()
    {
        $input = [
            'flag' => 'A',
            'done' => '',
        ];
        $this->validate->source($input);
        $this->validate->asText('flag');
        $this->validate->asText('done')->requiredIf('flag', ['a']);
        $this->assertEquals( true, $this->validate->passes() );

        $input = [
            'flag' => 'A',
            'done' => '',
        ];
        $validation = $this->factory->on($input);
        $validation->asText('flag')->string('lower');
        $validation->asText('done')->requiredIf('flag', ['a']);
        $this->assertEquals( true, $validation->fails() );
        $this->assertEquals( 'required item', $validation->message('done') );
    }
}