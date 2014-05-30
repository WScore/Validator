<?php
namespace WScore\Validation;

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
     * @return string
     */
    public static function locale( $locale=null )
    {
        if( !$locale ) return static::$locale;
        static::$locale = strtolower( locale_get_primary_language( $locale ) );
        return static::$locale;
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
        $message->setMessages( include($dir."validation.messages.php" ) );
        return $message;
    }

    /**
     * @param $messages
     */
    protected function setMessages( $messages )
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
     * @param $method
     * @param $parameter
     * @return string
     */
    public function find( $method, $parameter )
    {
        if( strpos( $method, '::filter_' ) !== false ) {
            $method = substr( $method, strpos( $method, '::filter_' )+9 );
        }
        if( isset( $this->messages[ $method ] ) )
        {
            // 2. use message for a specific method.
            if( !is_array($this->messages[ $method ]) ) {
                return $this->messages[ $method ];
            }
            // 3. use message for a method/parameter set.
            if( isset( $this->messages[ $method ][ $parameter ] ) ) {
                return $this->messages[ $method ][ $parameter ];
            }
        }
        // 4. use general error message.
        return $this->messages[ 0 ];
    }
    // +----------------------------------------------------------------------+
}