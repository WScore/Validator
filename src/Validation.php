<?php
namespace WScore\Validation;

/**
 * validates an array of values (i.e. input from html form).
 * 
 * @cacheable
 * 
 */
class Validation
{
    /**
     * @var array                 source of data to read from
     */
    protected $source = array();

    /**
     * @var array                 validated and invalidated data
     */
    protected $output = array();

    /**
     * @var array                 invalidated error messages
     */
    protected $errors = array();

    /**
     * @var int                   number of errors (invalids)
     */
    protected $err_num = 0;

    /**
     * @Inject
     * @var Validate
     */
    public $validate = null;

    // +----------------------------------------------------------------------+
    /**
     * @Inject
     * @param \WScore\Validation\Validate   $validate
     */
    public function __construct( $validate )
    {
        $this->validate = $validate;
    }

    /**
     * @param null|string $locale
     * @return static
     */
    public static function getInstance( $locale=null )
    {
        return new static( Validate::getInstance( $locale ) );
    }

    /**
     * @param array $data
     */
    public function source( $data=array() )
    {
        $this->source = $data;
    }

    /**
     * returns found value.
     * this method returns values that maybe invalid.
     *
     * @param null|string $key
     * @return array
     */
    public function pop( $key=null )
    {
        if( is_null( $key ) ) {
            return $this->output;
        }
        return Utils::arrGet( $this->output, $key );
    }

    /**
     * returns all the valid values.
     *
     * @return array
     */
    public function popSafe()
    {
        $safeData = $this->output;
        $this->_findClean( $safeData, $this->errors );
        return $safeData;
    }

    /**
     * @param array      $data
     * @param array|null $error
     */
    protected function _findClean( &$data, $error )
    {
        if( empty( $error ) ) return; // no error at all.
        foreach( $data as $key => $val ) {
            if( !array_key_exists( $key, $error ) ) {
                continue; // no error.
            }
            if( is_array( $data[ $key ] ) && is_array( $error[ $key ] ) ) {
                $this->_findClean( $data[$key], $error[$key] );
            }
            elseif( $error[$key]) { // error message exist.
                unset( $data[ $key ] );
            }
        }
    }

    /**
     * @param null|string $name
     * @return array|mixed
     */
    public function popError( $name=null )
    {
        if( $name ) return Utils::arrGet( $this->errors, $name );
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !$this->err_num;
    }

    // +----------------------------------------------------------------------+

    /**
     * pushes the $name.
     * returns the found value, or false if validation fails.
     *
     * @param string $name
     * @param array|Rules $rules
     * @return mixed
     */
    public function push( $name, $rules )
    {
        $found = $this->find( $name, $rules );
        $found = $this->validate->verify( $found, $rules, $errors );
        if( !$errors ) {
            $this->pushValue( $name, $found );
            return $found;
        }
        $this->pushError( $name, $errors, $found );
        if( is_array($found) ) {
            $this->_findClean( $found, $errors );
            return $found;
        }
        return false;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return Validation
     */
    public function pushValue( $name, $value )
    {
        $this->output[ $name ] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @param mixed  $error
     * @param bool|mixed $value
     * @return Validation
     */
    public function pushError( $name, $error, $value=false )
    {
        $this->errors[ $name ] = $error;
        $this->err_num ++;
        if( $value !== false ) $this->output[ $name ] = $value;
        return $this;
    }

    /**
     * finds a value with $name in the source data, applying the rules.
     *
     * @param string $name
     * @param array|Rules $rules
     * @return ValueTO|ValueTO[]
     */
    public function find( $name, &$rules )
    {
        // find a value from data source.
        $value = null;
        if( Utils::arrGet( $rules, 'multiple' ) ) {
            // check for multiple case i.e. Y-m-d.
            $value = Utils::prepare_multiple( $name, $this->source, $rules[ 'multiple' ] );
        }
        if( !$value && array_key_exists( $name, $this->source ) ) {
            // simplest case.
            $value = $this->source[ $name ];
        }
        // prepares filter for sameWith.
        $rules = Utils::prepare_sameWith( $this, $rules );
        return $value;
    }
    // +----------------------------------------------------------------------+
}