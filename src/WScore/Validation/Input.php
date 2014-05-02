<?php
namespace WScore\Validation;

/**
 * Class Input
 * A Facade class for Validation and Rules object. 
 *
 * @package WScore\Validation
 *
 * for starting a new validation.
 * @method static Validation text( string $name, array $filters=array() )
 *          
 * for accessing Validation object.
 * @method static source( array $source )
 * @method static pop( string $name=null )
 * @method static isValid()
 * @method static popError()
 */
class Input
{
    /**
     * @var Validation
     */
    public static $input;

    /**
     * @var Rules
     */
    public static $rules;

    /**
     * @var string
     */
    public static $locale;
    
    /**
     * set locale, and forge objects. 
     * 
     * @param $locale
     */
    public static function locale( $locale )
    {
        static::$locale = $locale;
        static::forge();
    }

    /**
     * forges Validation and Rules object if not set.
     * set $force to true to force to forge. 
     * 
     * @param bool $force
     */
    public static function forge($force=false)
    {
        if( $force || !static::$input ) {
            static::$input = Validation::getInstance( static::$locale );
        }
        if( $force || !static::$rules ) {
            static::$rules = Rules::getInstance( static::$locale );
        }
    }

    /**
     * @param string $method
     * @param array  $args
     * @throws \RuntimeException
     * @return mixed
     */
    public static function __callStatic( $method, $args )
    {
        if( in_array( $method, static::$rules->getTypeList() ) ) {
            $name = $args[0];
            array_shift( $args );
            return static::push( $name, $method, $args );
        }
        if( method_exists( static::$input, $method ) ) {
            return call_user_func_array( [static::$input, $method], $args );
        }
        throw new \RuntimeException( 'unknown rule type:'. $method );
    }

    /**
     * @param $name
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function push( $name, $method, $args=array() )
    {
        static::$rules->applyType( $method );
        foreach( $args as $filter ) {
            static::$rules->apply( $filter );
        }
        return static::$input->push( $name, static::$rules );
    }
}