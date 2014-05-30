<?php
namespace WScore\Validation;

use WScore\Validation\Locale\String as Locale;

class Message
{
    /**
     * @var array
     */
    public $messages = array();

    /**
     * @var string
     */
    protected static $locale = 'en';

    // +----------------------------------------------------------------------+
    public function __construct()
    {
    }

    /**
     * @param string $locale
     */
    public static function locale( $locale=null )
    {
        if( !$locale ) $locale = 'en';
        static::$locale = strtolower( locale_get_primary_language( $locale ) );
    }

    /**
     * @param null $locale
     * @param null $dir
     * @return Message
     */
    public static function getInstance( $locale=null, $dir=null )
    {
        if( !$locale ) $locale = static::$locale;
        if( !$dir ) $dir = __DIR__ . '/Locale/';
        $dir .= $locale . '/';

        /** @var Message $message */
        $message = new static();
        /** @noinspection PhpIncludeInspection */
        $message->loadMessage( include($dir."validation.messages.php" ) );
        return $message;
    }

    /**
     * @param $messages
     */
    protected function loadMessage( $messages )
    {
        $this->messages = $messages;
    }

    /**
     * find messages based on error type.
     * 1. use message if set.
     * 2. use message for a specific method. 
     * 3. use message for a method/parameter set. 
     * 4. use general error message. 
     * 
     * @param ValueTO $value
     */
    public function set( $value )
    {
        if( $value->getMessage() ) {
            // 1. use message if set.
            return;
        }
        $method = $value->getErrorMethod();
        if( strpos( $method, '::filter_' ) !== false ) {
            $method = substr( $method, strpos( $method, '::filter_' )+9 );
        }
        $parameter = $value->getParameter();
        if( !isset( $this->messages[ $method ] ) ) {
            // 4. use general error message. 
            $message = $this->messages[ 0 ];
        }
        elseif( !is_array( $this->messages[ $method ] ) ) {
            // 2. use message for a specific method. 
            $message = $this->messages[ $method ];
        }
        elseif( isset( $this->messages[ $method ][ $parameter ] ) ) {
            // 3. use message for a method/parameter set. 
            $message = $this->messages[ $method ][ $parameter ];
        }
        else {
            // 4. use general error message. 
            $message = $this->messages[ 0 ];
        }
        $value->setMessage( $message );
    }
    // +----------------------------------------------------------------------+
}