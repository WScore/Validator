<?php
namespace WStest02\Validation;

use WScore\Validation\Rules;
use WScore\Validation\Validation;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Story_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Validation */
    var $validate;

    /** @var  Rules */
    var $rules;

    public function setUp()
    {
        $this->validate = Validation::getInstance();
        $this->rules = new Rules();
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Validation', get_class( $this->validate ) );
    }

    /**
     * @test
     */
    function story_of_testing_bunch()
    {
        $input = array(
            'name' => 'valid story',
            'date' => '2014-05-01',
            'type1' => 1,
            'name1' => 'type name1',
            'type2' => 2,
            'name2' => 'type name2',
            'type3' => 3,
            'name3' => 'type name3',
        );
        $v = $this->validate;
        $v->source($input);

        // simple pop test
        $name = $v->push( 'name', Rules::text() );
        $this->assertEquals( $input['name'], $name );
        $date = $v->push( 'date', Rules::date() );
        $this->assertEquals( $input['date'], $date );

        $types = [1,2,3];
        foreach( $types as $type ) {
            $t = 'type'.$type;
            $n = 'name'.$type;
            $foundType = $v->push( $t, Rules::number() );

            if( 1 == $foundType ) {
                $v->push( $n, Rules::text() );
            } elseif( 2 == $foundType ) {
                $v->pushValue( $n, 'pushed name' );
            } else {
                $v->pushValue( $n, '' );
            }
        }
        $found = $v->pop();
        $this->assertEquals( $input['name1'], $found['name1'] );
        $this->assertEquals( 'pushed name', $found['name2'] );
        $this->assertEquals( '', $found['name3'] );
    }
}