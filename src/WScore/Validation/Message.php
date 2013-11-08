<?php
namespace WScore\Validation;

class Message
{
    public $messages = array();

    // +----------------------------------------------------------------------+
    public function __construct( $locale=null )
    {
        $this->messages = array(
            0           => 'invalid input',
            'encoding'  => 'invalid encoding',
            'required'  => 'required item',
            'choice'    => 'invalid choice',
            'sameAs'    => 'value not the same',
            'sameEmpty' => 'missing value to compare',
        );
    }

    /**
     * @param ValueTO $value
     */
    public function set( $value )
    {
        if( $value->getMessage() ) {
            return;
        }
        $method = $value->getErrorMethod();
        if( strpos( $method, '::filter_' ) !== false ) {
            $method = substr( $method, strpos( $method, '::filter_' )+9 );
        }
        if( isset( $this->messages[ $method ] ) ) {
            $message = $this->messages[ $method ];
        } else{
            $message = $this->messages[ 0 ];
        }
        $value->setMessage( $message );
    }
    // +----------------------------------------------------------------------+
}