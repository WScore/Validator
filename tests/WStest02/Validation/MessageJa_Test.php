<?php
namespace WStest02\Validation;

use WScore\Validation\Message;
use WScore\Validation\ValueTO;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class MessageJa_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Message */
    var $message;

    /** @var  \WScore\Validation\ValueTO */
    var $value;

    /**
     * @var array
     */
    var $msg;

    public function setUp()
    {
        $this->message = new Message( 'Ja' );
        $this->value   = new ValueTO();
        $this->msg     = include( __DIR__ . '/../../../src/WScore/Validation/Locale/Lang.ja.php' );
    }

    // +----------------------------------------------------------------------+
    /**
     * @test
     */
    function basic_class_type()
    {
        $this->assertEquals( 'WScore\Validation\Message', get_class( $this->message ) );
    }

    /**
     * @test
     */
    function value_with_error_sets_message()
    {
        // error happened in filter_required method.
        $this->value->setError( 'required' );
        $this->assertEquals( null, $this->value->message() );

        // now message is set for required method.
        $this->message->set( $this->value );
        $this->assertEquals( $this->msg[ 'required' ], $this->value->message() );
    }

    /**
     * @test
     */
    function matches_error_returns_japanese_message()
    {
        // error happened in filter_required method.
        $this->value->setError( 'matches', 'number' );
        $this->assertEquals( null, $this->value->message() );

        // now message is set for required method.
        $this->message->set( $this->value );
        $this->assertEquals( $this->msg[ 'matches' ][ 'number' ], $this->value->message() );
        
    }
}