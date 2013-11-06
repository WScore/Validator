<?php
namespace WScore\Validation;

class Filter
{
    public static $charCode = 'UTF-8';
    
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
    }
    
    /**
     * @param ValueTO $v
     * @param \Closure $closure
     */
    public function applyClosure( $v, $closure ) 
    {
        $val = $closure( $v->getValue() );
        $v->setValue( $val );
    }
    // +----------------------------------------------------------------------+
    //  filter definitions (filters that alters the value).
    // +----------------------------------------------------------------------+

    /**
     * sets error message.
     *
     * @param ValueTO $v
     * @param $p
     */
    public function filter_err_msg( $v, $p ) {
        $this->filter_message( $v, $p );
    }

    /**
     * sets error message.
     * 
     * @param ValueTO $v
     * @param $p
     */
    public function filter_message( $v, $p ) {
        if( $p ) $v->setMessage( $p );
    }

    /**
     * removes null from text. 
     * 
     * @param ValueTO $v
     */
    public function filter_noNull( $v ) {
        $v->setValue( str_replace( "\0", '', $v->getValue() ) );
    }

    /**
     * trims text. 
     * @param ValueTO $v
     */
    public function filter_trim( $v ) {
        $v->setValue( trim( $v->getValue() ) );
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
     * @param ValueTO $v
     * @param $p
     */
    public function filter_sanitize( $v, $p ) 
    {
        $option = Utils::arrGet( $this->sanitizes, $p, $p );
        $v->setValue( filter_var( $v->getValue(), $option ) );
    }

    /**
     * @param ValueTO $v
     * @param null $p
     */
    public function filter_encoding( $v, $p=null ) 
    {
        $code = ( empty( $p ) || $p === true ) ? static::$charCode: $p;
        if( !mb_check_encoding( $v->getValue(), $code ) ) {
            $v->setValue( '' ); // overwrite invalid encode string.
            $v->setError( __METHOD__, $p );
        }
    }

    public $mvConvert = array(
        'hankaku'  => 'aks',
        'han_kana' => 'kh',
        'zen_hira' => 'HVc',
        'zen_kana' => 'KVC',
    );

    /**
     * @param ValueTO $v
     * @param null $p
     */
    public function filter_mbConvert( $v, $p ) 
    {
        $convert = Utils::arrGet( $this->mvConvert, $p, 'KV' );
        $v->setValue( mb_convert_kana( $v->getValue(), $convert, static::$charCode ) );
    }

    /**
     * @param ValueTO $v
     * @param null $p
     */
    public function filter_string( $v, $p ) 
    {
        $val = $v->getValue();
        if( $p == 'lower' ) {
            $val = strtolower( $val );
        }
        elseif( $p == 'upper' ) {
            $val = strtoupper( $val );
        }
        elseif( $p == 'capital' ) {
            $val = ucwords( $val );
        }
        $v->setValue( $val );
    }

    /**
     * if the value is empty (false, null, empty string, or empty array), 
     * the default value of $p is used for the value. 
     *
     * @param ValueTO $v
     * @param $p
     */
    public function filter_default( $v, $p ) 
    {
        $val = $v->getValue();
        if( !$val && "" == "{$val}" ) { // no value. set default...
            $v->setValue( $p );
        }
    }
    // +----------------------------------------------------------------------+
    //  filter definitions (filters for validation).
    // +----------------------------------------------------------------------+

    /**
     * checks if the $value has some value.
     * @param ValueTO $v
     */
    public function filter_required( $v ) 
    {
        $val = $v->getValue();
        if( "{$val}" === '' ) { 
            // the value is empty. check if it is "required".
            $v->setError( __METHOD__ );
        }
    }

    /**
     * breaks loop if value is empty by returning $loop='break'.
     * validation is not necessary for empty value.
     * @param ValueTO $v
     */
    public function filter_loopBreak( $v ) 
    {
        $val = $v->getValue();
        if( "{$val}" == '' ) { // value is really empty. break the loop.
            $v->setBreak( true ); // skip subsequent validations for empty values.
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

    /**
     * @param ValueTO $v
     * @param $p
     */
    public function filter_matches( $v, $p ) 
    {
        $option  = Utils::arrGet( $this->matchType, $p, $p );
        if( !preg_match( "/^{$option}\$/", $v->getValue() ) ) {
            $v->setError( __METHOD__, $p );
        }
    }
    
    /**
     * @param ValueTO $v
     * @param $p
     */
    public function filter_pattern( $v, $p ) {
        if( !preg_match( "/^{$p}\$/", $v->getValue() ) ) {
            $v->setError( __METHOD__, $p );
        }
    }

    /**
     * @param ValueTO $v
     * @param $p
     */
    public function filter_sameAs( $v, $p ) 
    {
        if( $v->getValue() === $p ) {
            $v->setError( __METHOD__, $p );
        }
    }

    /**
     * @param ValueTO $v
     */
    public function filter_sameEmpty( $v ) 
    {
        $val = $v->getValue();
        if( "{$val}" !== "" ) {
            $v->setError( __METHOD__ );
        }
    }
    // +----------------------------------------------------------------------+
}
