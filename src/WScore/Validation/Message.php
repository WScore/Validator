<?php
namespace WScore\Validation;

use WScore\DiContainer\Types\String as Locale;

class Message
{
    /**
     * @var array
     */
    public $messages = array();

    /**
     * @Inject
     * @var Locale
     */
    public $locale = 'en';

    // +----------------------------------------------------------------------+
    public function __construct( $locale=null )
    {
        if( $locale ) $this->locale = $locale;
        $this->loadMessage();
    }
    
    public function loadMessage( $filename=null )
    {
        if( !$filename ) {
            $locale = strtolower( locale_get_primary_language( $this->locale ) );
            $filename = __DIR__ . "/Locale/Lang.{$locale}.php";
        }
        $this->messages = include( $filename ); 
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
        $parameter = $value->getParameter();
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