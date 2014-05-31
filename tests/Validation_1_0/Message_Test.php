<?php
namespace tests\Validation_1_0;

use WScore\Validation\Message;

require_once( dirname( __DIR__ ) . '/autoloader.php' );

class Message_Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Message
     */
    public $m;

    public $msg;
    
    function setup()
    {
        $this->make();
    }

    function make($locale='en') {
        $this->m = Message::getInstance($locale);
        /** @noinspection PhpIncludeInspection */
        $this->msg = include( dirname(dirname(__DIR__))."/src/Locale/{$locale}/validation.messages.php");
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Validation\Message', get_class( $this->m ) );
    }

    /**
     * @test
     */
    function find_generic_message()
    {
        $message = $this->m->find( 'no-such-method', null );
        $this->assertEquals( $this->msg[0], $message );
    }

    /**
     * @test
     */
    function find_method_message_of_required()
    {
        $message = $this->m->find( 'required', null );
        $this->assertEquals( $this->msg['required'], $message );
    }

    /**
     * @test
     */
    function find_method_message_of_required_with_filter()
    {
        $message = $this->m->find( 'what-ever::filter_required', null );
        $this->assertEquals( $this->msg['required'], $message );
    }

    /**
     * @test
     */
    function find_method_and_parameter_message()
    {
        $message = $this->m->find( 'matches', 'code' );
        $this->assertEquals( $this->msg['matches']['code'], $message );
    }

    /**
     * @test
     */
    function find_method_and_parameter_but_not_matched_message()
    {
        $message = $this->m->find( 'matches', 'not-valid' );
        $this->assertEquals( $this->msg[0], $message );
    }
}