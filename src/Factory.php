<?php
namespace WScore\Validation;

use WScore\Validation\Utils\Filter;
use WScore\Validation\Utils\Message;
use WScore\Validation\Utils\ValueTO;

class Factory
{
    static $locale = 'en';
    
    static $dir = null;

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
        Rules::locale( $locale, static::getDir() );
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
     * @return Dio
     */
    public static function input( $data=null )
    {
        $input = static::buildDio();
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
     * @return Utils\Message
     */
    public static function buildMessage( $locale=null, $dir=null )
    {
        if( !$locale ) $locale = static::getLocale();
        if( !$dir ) $dir = static::getDir();
        return new Message( $locale, $dir );
    }

    /**
     * @param string $locale
     * @param string $dir
     * @return Utils\ValueTO
     */
    public static function buildValueTO( $locale=null, $dir=null )
    {
        return new ValueTO( static::buildMessage( $locale, $dir ) );
    }

    /**
     * @return Utils\Filter
     */
    public static function buildFilter()
    {
        return new Filter();
    }

    /**
     * @param string $locale
     * @param string $dir
     * @return Verify
     */
    public static function buildVerify( $locale=null, $dir=null )
    {
        return new Verify( static::buildFilter(), static::buildValueTO( $locale, $dir ) );
    }

    /**
     * @param string $locale
     * @param string $dir
     * @return Dio
     */
    public static function buildDio( $locale=null, $dir=null )
    {
        return new Dio( static::buildVerify( $locale, $dir ) );
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
        return new Rules( $locale, $dir );
    }
}