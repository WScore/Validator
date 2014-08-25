<?php
namespace WScore\Validation;

class Validation
{
    /**
     * @param string $locale
     * @param string|null $dir
     */
    public static function locale( $locale, $dir=null )
    {
        Factory::setLocale( $locale, $dir );
    }

    /**
     * @param array $source
     * @return Dio
     */
    public static function on( $source )
    {
        $v = Factory::buildValidation();
        $v->source( $source );
        return $v;
    }

    /**
     * @param array  $source
     * @param string $idx
     * @param bool   $useUnIndexed
     * @return Dio
     */
    public static function onIndex( $source, $idx, $useUnIndexed=false )
    {
        $input = array();
        foreach( $source as $key => $data ) {
            if( !is_array($data) && $useUnIndexed ) {
                $input[$key] = $data;
            } elseif( isset($data[$idx]) ) {
                $input[$key] = $data[$idx];
            }
        }
        return static::on($input);
    }

    /**
     * @param string $type
     * @return Rules
     */
    public static function rule( $type='text' )
    {
        $rule = Factory::buildRules();
        $rule->applyType( $type );
        return $rule;
    }
}