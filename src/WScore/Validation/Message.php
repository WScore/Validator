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
            'matches'   => [
                'number' => 'only number',
                'int'    => 'not an integer',
                'float'  => 'not a float number',
                'code'   => 'only alpha-numeric code',
                'mail'   => 'not a valid mail address',
            ],
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
        $parameter = $value->getType();
        if( !isset( $this->messages[ $method ] ) ) {
            $message = $this->messages[ 0 ];
        }
        elseif( !is_array( $this->messages[ $method ] ) ) {
            $message = $this->messages[ $method ];
        }
        elseif( isset( $this->messages[ $method ][ $parameter ] ) ) {
            $message = $this->messages[ $method ][ $parameter ];
        }
        else {
            $message = $this->messages[ 0 ];
        }
        $value->setMessage( $message );
    }
    // +----------------------------------------------------------------------+
}