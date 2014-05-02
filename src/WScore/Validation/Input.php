<?php
namespace WScore\Validation;

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
     * @param $locale
     */
    public static function locale( $locale )
    {
        static::$locale = $locale;
        static::forge();
    }

    /**
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
     * @return mixed
     */
    public static function __callStatic( $method, $args )
    {
        if( in_array( $method, [ 'text', ] ) ) {
            $name = $args[0];
            array_shift( $args );
            return static::push( $name, $method, $args );
        }
    }

    /**
     * @param $name
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function push( $name, $method, $args )
    {
        static::$rules->applyType( $method );
        foreach( $args as $filter ) {
            static::$rules->apply( $filter );
        }
        return static::$input->push( $name, static::$rules );
    }
}