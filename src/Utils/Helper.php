<?php
namespace WScore\Validation\Utils;

use WScore\Validation\Dio;
use WScore\Validation\Rules;

class Helper
{
    /**
     * @param            $arr
     * @param            $key
     * @param null|mixed $default
     * @return mixed
     */
    public static function arrGet($arr, $key, $default = null)
    {
        if (!is_string($key)) {
            return $default;
        }
        if (!is_array($arr) && (is_object($arr) && !($arr instanceof \ArrayAccess))) {
            return $default;
        }

        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    /**
     * converts string filter to array. string in: 'rule1:parameter1|rule2:parameter2'
     *
     * @param string|array $filter
     * @param bool         $type
     * @return array
     */
    public static function convertFilter($filter, $type = false)
    {
        if (!$filter) {
            return array();
        }
        if (is_array($filter)) {
            return $filter;
        }

        $filter_array = array();
        $rules        = explode('|', $filter);
        foreach ($rules as $rule) {
            $filter = explode(':', $rule, 2);
            array_walk($filter, function (&$v) {
                $v = trim($v);
            });
            if (isset($filter[1])) {
                $filter_array[$filter[0]] = ($filter[1] == 'FALSE') ? false : $filter[1];
            } else {
                if ($type && !isset($filter_array['type'])) {
                    $filter_array['type'] = $filter[0];
                } else {
                    $filter_array[$filter[0]] = true;
                }
            }
        }

        return $filter_array;
    }

    // +----------------------------------------------------------------------+
    //  special filters for multiple and sameWith rules.
    // +----------------------------------------------------------------------+
    /**
     * @var array   options for multiple preparation.
     */
    public static $multiples = array(
        'date'     => array('suffix' => 'y,m,d', 'connector' => '-',),
        'YMD'      => array('suffix' => 'y,m,d', 'connector' => '-',),
        'YM'       => array('suffix' => 'y,m', 'connector' => '-',),
        'time'     => array('suffix' => 'h,i,s', 'connector' => ':',),
        'His'      => array('suffix' => 'h,i,s', 'connector' => ':',),
        'Hi'       => array('suffix' => 'h,i', 'connector' => ':',),
        'datetime' => array('suffix' => 'y,m,d,h,i,s', 'format' => '%04d-%02d-%02d %02d:%02d:%02d',),
        'tel'      => array('suffix' => '1,2,3', 'connector' => '-',),
        'credit'   => array('suffix' => '1,2,3,4', 'connector' => '',),
        'amex'     => array('suffix' => '1,2,3', 'connector' => '',),
    );

    /**
     * prepares for validation by creating a value from multiple value.
     *
     * @param string       $name
     * @param array        $source
     * @param string|array $option
     * @return mixed|null|string
     */
    public static function prepare_multiple($name, $source, $option)
    {
        // get options.
        if (is_string($option)) {
            $option = self::arrGet(self::$multiples, $option, array());
        }
        $sep = array_key_exists('separator', $option) ? $option['separator'] : '_';
        $con = array_key_exists('connector', $option) ? $option['connector'] : '-';
        // find multiples values from suffix list.
        $lists  = array();
        $suffix = explode(',', $option['suffix']);
        foreach ($suffix as $sfx) {
            $name_sfx = $name . $sep . $sfx;
            if (array_key_exists($name_sfx, $source) && trim($source[$name_sfx])) {
                $lists[] = trim($source[$name_sfx]);
            }
        }
        // merge the found list into one value.
        $found = null; // default is null if list was not found.
        if (!empty($lists)) {
            // found format using sprintf.
            if (isset($option['format'])) {
                $param = array_merge(array($option['format']), $lists);
                $found = call_user_func_array('sprintf', $param);
            } else {
                $found = implode($con, $lists);
            }
        }

        return $found;
    }

    // +----------------------------------------------------------------------+
    /**
     * prepares filter for sameWith rule.
     * get another value to compare in sameWith, and compare it with the value using sameAs rule.
     *
     * @param Dio         $dio
     * @param array|Rules $rules
     * @return array|Rules
     */
    public static function prepare_sameWith($dio, $rules)
    {
        if (!self::arrGet($rules, 'sameWith')) {
            return $rules;
        }
        // find the same with value.
        $sub_name = $rules['sameWith'];
        if (is_object($rules)) {
            $sub_filter = clone $rules;
        } else {
            $sub_filter = $rules;
        }
        $sub_filter['sameWith'] = false;
        $sub_filter['required'] = false;
        $value                  = $dio->find($sub_name, $sub_filter);
        $value                  = $dio->verify->is($value, $sub_filter);

        // reset sameWith filter, and set same{As|Empty} filter.
        $rules['sameWith'] = false;
        if ($value) {
            $rules['sameAs'] = $value;
        } else {
            $rules['sameEmpty'] = true;
        }

        return $rules;
    }

    /**
     * @param Dio         $dio
     * @param array|Rules $rules
     * @return array|Rules
     */
    public static function prepare_requiredIf($dio, $rules)
    {
        if (!self::arrGet($rules, 'requiredIf')) {
            return $rules;
        }
        $args = $rules['requiredIf'];
        if (!is_array($args)) {
            $flag_name = $args;
            $flags_in  = null;
        } else {
            $flag_name = $args[0];
            $flags_in  = array_key_exists(1, $args) ? (array)$args[1] : null;
        }
        $flag_value = $dio->get($flag_name);
        if ((string)$flag_value === '') {
            return $rules;
        }
        if ($flags_in && !in_array($flag_value, $flags_in)) {
            return $rules;
        }
        $rules['required'] = true;

        return $rules;
    }

}