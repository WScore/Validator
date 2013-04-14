<?php
namespace WScore\Validation;

class Message
{
    /** @var array                  error message for some filters.  */
    public $filterErrMsg = array();
    
    /** @var array                  error message for each types.    */
    public $typeErrMsg = array();
    
    protected $locale = 'en_US';

    // +----------------------------------------------------------------------+
    public function __construct( $locale=null )
    {
        if( $locale ) {
            $this->setLocale( $locale );
        }
        $this->loadMessage();
    }

    /**
     * @param string $locale
     */
    public function setLocale( $locale ) 
    {
        $this->locale = $locale;
    }

    /**
     * @param null|string $messageFile
     */
    public function loadMessage( $messageFile=null ) 
    {
        if( !$messageFile ) $messageFile = __DIR__ . "/Locale/Lang.{$this->locale}.php";
        if( file_exists( $messageFile ) ) {
            $messages = include( $messageFile );
            $this->filterErrMsg = $messages[ 'filter' ];
            $this->typeErrMsg   = $messages[ 'type' ];
        }
    }
    /**
     * returns an error message from error information.
     * the error message will be:
     *   - $this->message if it is set,
     *   - filterErrMsg[ $rule ] if the value is set,
     *   - err_msg if the it is given, and
     *   - typeErrMsg[ $type ] if the value is set.
     *
     * @param array  $error
     * @param string $err_msg
     * @param null   $type
     * @return string
     */
    public function message( $error, $err_msg='', $type=null )
    {
        // is it really an error?
        if( !$error || empty( $error ) ) return '';
        
        // 1. find rule and option of the last error.
        if( !is_array( $error ) ) return $err_msg;
        $keys    = key( $error );
        $rule    = (is_array( $keys ) ) ? end( $keys ) : $keys;
        $option  = $error[ $rule ];
        
        // 2. return filter specific message if it is set. 
        if( isset( $this->filterErrMsg[ $rule ] ) ) {
            return $this->filterErrMsg[ $rule ];
        }
        // 3. return message for this specific value. 
        if( $err_msg ) {
            return $err_msg;
        }
        // 4. return message based on this type, if type is set. 
        if( isset( $type ) && isset( $this->typeErrMsg[ $type ] ) ) {
            return $this->typeErrMsg[ $type ];
        }
        // 5. return generic error message based on rule/option. 
        $err_msg = "invalid {$rule}";
        if( $option ) $err_msg .= " with {$option}";
        return $err_msg;
    }
    // +----------------------------------------------------------------------+
}