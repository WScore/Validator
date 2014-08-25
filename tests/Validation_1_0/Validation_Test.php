<?php
namespace tests\Validation_1_0;

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
}