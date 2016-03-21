<?php
namespace tests\Validation_1_0;

require_once(dirname(__DIR__).'/autoloader.php');

use WScore\Validation\ValidationFactory;

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $factory = new ValidationFactory();
        $v = $factory->on( [ 'test' => 'tested' ] );
        $this->assertEquals( 'WScore\Validation\Dio', get_class($v) );

        $is = $v->is( 'test', $factory->rules()->withType('text') );
        $this->assertEquals( 'tested', $is );
    }

    function test_locale()
    {
        $factory = new ValidationFactory();
        $v = $factory->on([]);
        $v->is( 'test', $factory->rules()->withType('text')->required() );
        $this->assertEquals( 'required item', $v->message('test') );

        $factory = new ValidationFactory('ja');
        $v = $factory->on([]);
        $v->is( 'test', $factory->rules()->withType('text')->required() );
        $this->assertEquals( '必須項目です', $v->message('test') );
    }

    /**
     * @test
     */
    function filter_invalid_integer_input()
    {
        $factory = new ValidationFactory();
        $v = $factory->on([
            'int' => '100',
            'big' => '101',
            'bad' => '12345678901234567890123456789012345678901234567890',
        ]);
        $value1 = (int) $v->is( 'int', $factory->rules()->withType('integer')->required()->max(100) );
        $value2 = (int) $v->is( 'big', $factory->rules()->withType('integer')->required()->max(100) );
        $value3 = (int) $v->is( 'bad', $factory->rules()->withType('integer')->required() );

        $this->assertEquals('100',  $value1);
        $this->assertEquals( false, $value2);
        $this->assertEquals( false, $value3);
        $this->assertEquals( 'exceeds max value', $v->message('big') );
        $this->assertEquals( 'required item', $v->message('bad') );
    }

    /**
     * @test
     */
    function filter_min_value()
    {
        $factory = new ValidationFactory();
        $v = $factory->on([
            'int' => '100',
            'big' => '101',
            'bad' => '12345678901234567890123456789012345678901234567890',
        ]);
        $v->asInteger('int')->min(101);
        $v->asInteger('big')->min(101);
        $value1 = $v->get('int');
        $value2 = $v->get('big');

        $this->assertEquals(false,  $value1);
        $this->assertEquals( '101', $value2);
        $this->assertEquals( 'below min value', $v->message('int') );
    }
}