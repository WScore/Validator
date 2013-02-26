<?php
namespace WScore\Validation;

class Filter
{
    public static $charCode = 'UTF-8';
    
    /** @var null|string     value modified by filters */
    public $value = null;
    
    /** @var null|string     set to method if error */
    public $error = null;

    /** @var bool            breaks the loop */
    public $break = false;

    /** @var string          error messages */
    public $err_msg = '';

    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }
    /**
     * @param string        $value
     */
    public function setup( $value )
    {
        $this->value = $value;
        $this->error = null;
        $this->break = false;
        $this->err_msg = '';
    }

    /**
     * @param array  $arr
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function arrGet( $arr, $key, $default=null ) {
        if( !is_array( $arr ) ) return $default;
        return array_key_exists( $key, $arr ) ? $arr[$key] : $default;
    }

    /**
     * @param \Closure $closure
     * @param $p
     */
    public function applyClosure( $closure, $p ) {
        $return = $closure( $this->value, $p );
        if( !$return ) $this->error = true;
    }

    /**
     * sets internal error information: $this->error = [ rule => option ]
     *
     * @param string      $method
     * @param null|string $p
     */
    public function error( $method, $p=null ) {
        $method = substr( $method, strrpos( $method, '::filter_' )+9 );
        $error = array( $method => $p );
        $this->error = $error;
    }
    // +----------------------------------------------------------------------+
    //  filter definitions (filters that alters the value).
    // +----------------------------------------------------------------------+

    /**
     * sets error message for this filter.
     *
     * @param $p
     */
    public function filter_err_msg( $p ) {
        $this->filter_message( $p );
    }
    public function filter_message( $p ) {
        if( $p ) $this->err_msg = $p;
    }

    /**
     * removes null from text. 
     */
    public function filter_noNull() {
        $this->value = str_replace( "\0", '', $this->value );
    }

    /**
     * trims text. 
     */
    public function filter_trim() {
        $this->value = trim( $this->value );
    }

    /**
     * options for sanitize.
     * @var array
     */
    public $sanitizes = array(
        'mail'   => FILTER_SANITIZE_EMAIL,
        'float'  => FILTER_SANITIZE_NUMBER_FLOAT,
        'int'    => FILTER_SANITIZE_NUMBER_INT,
        'url'    => FILTER_SANITIZE_URL,
        'string' => FILTER_SANITIZE_STRING,
    );

    /**
     * sanitize the value using filter_var. 
     * @param $p
     */
    public function filter_sanitize( $p ) {
        $option = $this->arrGet( $this->sanitizes, $p, $p );
        $this->value = filter_var( $this->value, $option );
    }

    public function filter_encoding( $p=null ) {
        $code = ( empty( $p ) || $p === true ) ? static::$charCode: $p;
        if( !mb_check_encoding( $this->value, $code ) ) {
            $this->value = ''; // overwrite invalid encode string.
            $this->error( __METHOD__, $p );
        }
    }

    public $mvConvert = array(
        'hankaku'  => 'aks',
        'han_kana' => 'kh',
        'zen_hira' => 'HVc',
        'zen_kana' => 'KVC',
    );
    public function filter_mbConvert( $p ) {
        $convert = $this->arrGet( $this->mvConvert, $p, 'KV' );
        $this->value = mb_convert_kana( $this->value, $convert, static::$charCode );
    }

    public function filter_string( $p ) {
        if( $p == 'lower' ) {
            $this->value = strtolower( $this->value );
        }
        elseif( $p == 'upper' ) {
            $this->value = strtoupper( $this->value );
        }
        elseif( $p == 'capital' ) {
            $this->value = ucwords( $this->value );
        }
    }

    /**
     * if the value is empty (false, null, empty string, or empty array), 
     * the default value of $p is used for the value. 
     * 
     * @param $p
     */
    public function filter_default( $p ) {
        if( !$this->value && "" == "{$this->value}" ) { // no value. set default...
            $this->value = $p;
        }
    }
    // +----------------------------------------------------------------------+
    //  filter definitions (filters for validation).
    // +----------------------------------------------------------------------+

    /**
     * checks if the $value has some value.
     */
    public function filter_required() {
        if( "{$this->value}" === '' ) { 
            // the value is empty. check if it is "required".
            $this->error( __METHOD__ );
        }
    }

    /**
     * breaks loop if value is empty by returning $loop='break'.
     * validation is not necessary for empty value.
     */
    public function filter_loopBreak() {
        if( "{$this->value}" == '' ) { // value is really empty. break the loop.
            $this->break = true; // skip subsequent validations for empty values.
        }
    }

    /**
     * options for patterns.
     * @var array
     */
    public $matchType = array(
        'number' => '[0-9]+',
        'int'    => '[-0-9]+',
        'float'  => '[-.0-9]+',
        'code'   => '[-_0-9a-zA-Z]+',
        'mail'   => '[a-zA-Z0-9_.-]+@[a-zA-Z0-9_.-]+\.[a-zA-Z]+',
    );

    public function filter_matches( $p ) {
        $option  = $this->arrGet( $this->matchType, $p, $p );
        if( !preg_match( "/^{$option}\$/", $this->value ) ) {
            $this->error( __METHOD__, $p );
        }
    }
    /**
     * @param $p
     */
    public function filter_pattern( $p ) {
        if( !preg_match( "/^{$p}\$/", $this->value ) ) {
            $this->error( __METHOD__, $p );
        }
    }

    public function filter_sameAs( $p ) {
        return $this->value===$p;
    }

    public function filter_sameEmpty() {
        if( "{$this->value}" !== "" ) {
            $this->error( __METHOD__ );
        }
    }
    // +----------------------------------------------------------------------+
}
