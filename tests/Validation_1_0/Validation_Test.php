<?php
namespace tests\Validation_1_0;

require_once(dirname(__DIR__).'/autoloader.php');

use WScore\Validation\Rules;
use WScore\Validation\ValidationFactory;

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $factory = new ValidationFactory();
        $v = $factory->on( [ 'test' => 'tested' ] );
        $this->assertEquals( 'WScore\Validation\Dio', get_class($v) );

        $is = $v->is( 'test', Rules::text() );
        $this->assertEquals( 'tested', $is );
    }

    function test_locale()
    {
        $factory = new ValidationFactory();
        $v = $factory->on([]);
        $v->is( 'test', Rules::text()->required() );
        $this->assertEquals( 'required item', $v->message('test') );

        $factory = new ValidationFactory('ja');
        $v = $factory->on([]);
        $v->is( 'test', Rules::text()->required() );
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
        $value1 = (int) $v->is( 'int', Rules::integer()->required()->max(100) );
        $value2 = (int) $v->is( 'big', Rules::integer()->required()->max(100) );
        $value3 = (int) $v->is( 'bad', Rules::integer()->required() );

        $this->assertEquals('100',  $value1);
        $this->assertEquals( false, $value2);
        $this->assertEquals( false, $value3);
        $this->assertEquals( 'exceeds max value', $v->message('big') );
        $this->assertEquals( 'required item', $v->message('bad') );
    }
}