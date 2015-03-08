<?php
namespace tests\Validation_1_0;

use WScore\Validation\Utils\Message;

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
        $this->m = new Message($locale);
        /** @noinspection PhpIncludeInspection */
        $this->msg = include( dirname(dirname(__DIR__))."/src/Locale/{$locale}/validation.messages.php");
    }

    function test0()
    {
        $this->assertEquals( 'WScore\Validation\Utils\Message', get_class( $this->m ) );
    }

    /**
     * @test
     */
    function find_generic_message()
    {
        $message = $this->m->find( 'text', 'no-such-method', null );
        $this->assertEquals( $this->msg[0], $message );
    }

    /**
     * @test
     */
    function find_method_message_of_required()
    {
        $message = $this->m->find( 'text', 'required', null );
        $this->assertEquals( $this->msg['required'], $message );
    }

    /**
     * @test
     */
    function find_method_message_of_required_with_filter()
    {
        $message = $this->m->find( 'text', 'what-ever::filter_required', null );
        $this->assertEquals( $this->msg['required'], $message );
    }

    /**
     * @test
     */
    function find_method_and_parameter_message()
    {
        $message = $this->m->find( 'text', 'matches', 'code' );
        $this->assertEquals( $this->msg['matches']['code'], $message );
    }

    /**
     * @test
     */
    function find_method_and_parameter_but_not_matched_message()
    {
        $message = $this->m->find( 'text', 'matches', 'not-valid' );
        $this->assertEquals( $this->msg[0], $message );
    }

    /**
     * @test
     */
    function find_message_by_mail_type()
    {
        $message = $this->m->find( 'mail', 'no-method', 'not-valid' );
        $this->assertEquals( $this->msg['_type_']['mail'], $message );
    }
}