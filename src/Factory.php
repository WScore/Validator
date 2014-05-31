<?php
namespace WScore\Validation;

class Factory
{
    static $locale = 'en';
    
    static $dir = null;

    static $message = '\WScore\Validation\Message';

    static $validate = '\WScore\Validation\Validate';

    static $validation = '\WScore\Validation\Validation';

    static $filter = '\WScore\Validation\Filter';

    static $valueTO = '\WScore\Validation\ValueTO';
    
    static $rules = '\WScore\Validation\Rules';

    /**
     * @param string $locale
     * @internal param string $dir
     */
    public static function setLocale( $locale )
    {
        static::$locale = strtolower( locale_get_primary_language( $locale ) );
        if( func_num_args() > 1 ) {
            static::$dir = func_get_arg(1);
        }
        /*
         * set up locale for Rules, which is often called by static. 
         */
        /** @var Rules $class */
        $class = static::$rules;
        $class::locale( $locale, static::getDir() );
    }
    
    /**
     * @return string
     */
    public static function getDir()
    {
        return static::$dir;
    }

    /**
     * @return string
     */
    public static function getLocale()
    {
        return static::$locale;
    }

    /**
     * @param null|array $data
     * @return Validation
     */
    public static function input( $data=null )
    {
        $input = static::buildValidation();
        if( $data && is_array($data) ) {
            $input->source( $data );
        } else {
            $input->source( $_POST );
        }
        return $input;
    }
    
    /**
     * @param string $locale
     * @param string $dir
     * @return Message
     */
    public static function buildMessage( $locale=null, $dir=null )
    {
        if( !$locale ) $locale = static::getLocale();
        if( !$dir ) $dir = static::getDir();
        /** @var Message $class */
        $class = static::$message;
        return new $class( $locale, $dir );
    }

    /**
     * @param string $locale
     * @param string $dir
     * @return ValueTO
     */
    public static function buildValueTO( $locale=null, $dir=null )
    {
        /** @var ValueTO $class */
        $class = static::$valueTO;
        return new $class( static::buildMessage( $locale, $dir ) );
    }

    /**
     * @return Filter
     */
    public static function buildFilter()
    {
        /** @var Filter $class */
        $class = static::$filter;
        return new $class();
    }

    /**
     * @param string $locale
     * @param string $dir
     * @return Validate
     */
    public static function buildValidate( $locale=null, $dir=null )
    {
        /** @var Validate $class */
        $class = static::$validate;
        return new $class( static::buildFilter(), static::buildValueTO( $locale, $dir ) );
    }

    /**
     * @param string $locale
     * @param string $dir
     * @return Validation
     */
    public static function buildValidation( $locale=null, $dir=null )
    {
        /** @var Validation $class */
        $class = static::$validation;
        return new $class( static::buildValidate( $locale, $dir ) );
    }

    /**
     * @param string $locale
     * @param string $dir
     * @return Rules
     */
    public static function buildRules( $locale=null, $dir=null )
    {
        if( !$locale ) $locale = static::getLocale();
        if( !$dir ) $dir = static::getDir();
        /** @var Rules $class */
        $class = static::$rules;
        return new $class( $locale, $dir );
    }
}