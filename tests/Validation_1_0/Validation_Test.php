<?php
namespace tests\Validation_1_0;

require_once(dirname(__DIR__).'/autoloader.php');

use WScore\Validation\Factory;
use WScore\Validation\Rules;
use WScore\Validation\Validation;

class Validation_Test extends \PHPUnit_Framework_TestCase
{
    function test0()
    {
        $v = Validation::on( [ 'test' => 'tested' ] );
        $this->assertEquals( 'WScore\Validation\Dio', get_class($v) );

        $is = $v->is( 'test', Rules::text() );
        $this->assertEquals( 'tested', $is );
    }

    function test_useInstance()
    {
        $v = Factory::buildDio();
        Validation::useInstance( $v );
        $v2 = Validation::on( ['test'=>'tested'] );
        $this->assertSame( $v, $v2 );
        Validation::useInstance(null);
    }

    function test_locale()
    {
        Validation::locale('en');
        $v = Validation::on([]);
        $v->is( 'test', Rules::text()->required() );
        $this->assertEquals( 'required item', $v->message('test') );

        Validation::locale('ja');
        $v = Validation::on([]);
        $v->is( 'test', Rules::text()->required() );
        $this->assertEquals( '必須項目です', $v->message('test') );
    }

    /**
     * @test
     */
    function filter_invalid_integer_input()
    {
        Validation::locale('en');
        $v = Validation::on([
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