<?php
namespace WScore\Validation;

class Validation
{
    protected static $dio = null;

    public static function useInstance($obj)
    {
        static::$dio = $obj;
    }

    /**
     * @param string      $locale
     * @param string|null $dir
     */
    public static function locale($locale, $dir = null)
    {
        Factory::setLocale($locale, $dir);
    }

    /**
     * @param array $source
     * @return Dio
     */
    public static function on($source)
    {
        $v = static::$dio ?: Factory::buildDio();
        $v->source($source);

        return $v;
    }
}