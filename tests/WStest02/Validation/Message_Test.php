<?php
namespace WStest02\Validation;

use WScore\Validation\Message;
use WScore\Validation\ValueTO;

require_once( dirname( dirname( __DIR__ ) ) . '/autoloader.php' );

class Message_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Validation\Message */
    var $message;

    /** @var  \WScore\Validation\ValueTO */
    var $value;

    public function setUp()
    {
        $this->message = new Message();
        $this->value   = new ValueTO();
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
        $this->assertEquals( null, $this->value->getMessage() );

        // now message is set for required method.
        $this->message->set( $this->value );
        $this->assertEquals( 'required item', $this->value->getMessage() );
    }

    /**
     * @test
     */
    function not_new_message_is_set_if_already_set()
    {
        // no message.
        $this->assertEquals( null, $this->value->getMessage() );

        // set message, 'tested'
        $this->value->setMessage( 'tested' );
        $this->assertEquals( 'tested', $this->value->getMessage() );

        // message is the same as before.
        $this->message->set( $this->value );
        $this->assertEquals( 'tested', $this->value->getMessage() );
    }

    /**
     * @test
     */
    function value__with_error_sets_generic_message()
    {
        // error happened in filter_required method.
        $this->value->setError( 'a_generic_error' );
        $this->assertEquals( null, $this->value->getMessage() );

        // now message is set for required method.
        $this->message->set( $this->value );
        $this->assertEquals( $this->message->messages[0], $this->value->getMessage() );
    }
}