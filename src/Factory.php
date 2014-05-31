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
     * @param string $dir
     */
    public static function setDir( $dir )
    {
        static::$dir = $dir;
    }

    /**
     * @param string $locale
     */
    public static function setLocale( $locale )
    {
        static::$locale = locale_get_primary_language( $locale );
        /** @var Rules $class */
        $class = static::$message;
        $class::getInstance( $locale, static::getDir() );
    }
    
    /**
     * @return string
     */
    public static function getDir()
    {
        if( !static::$dir ) {
            static::$dir = __DIR__ . '/Locale';
        }
        return static::$dir;
    }

    /**
     * @return Message
     */
    public static function buildMessage()
    {
        /** @var Message $class */
        $class = static::$message;
        return $class::getInstance( static::$locale, static::getDir() );
    }

    /**
     * @return ValueTO
     */
    public static function buildValueTO()
    {
        /** @var ValueTO $class */
        $class = static::$valueTO;
        return new $class( static::buildMessage() );
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
     * @return Validate
     */
    public static function buildValidate()
    {
        /** @var Validate $class */
        $class = static::$validate;
        return new $class( static::buildFilter(), static::buildValueTO() );
    }

    /**
     * @return Validation
     */
    public static function buildValidation()
    {
        /** @var Validation $class */
        $class = static::$validation;
        return new $class( static::buildValidate() );
    }
}