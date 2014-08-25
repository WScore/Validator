<?php
namespace WScore\Validation;

use WScore\Validation\Utils\ValueTO;

/**
 * Class Dio
 * @package WScore\Validation
 *
 * Data Import Object
 * for validating a adata, an array of values (i.e. input from html form).
 */
class Dio
{
    /**
     * @var array                 source of data to read from
     */
    protected $source = array();

    /**
     * @var array                 validated and invalidated data
     */
    protected $found = array();

    /**
     * @var array                 invalidated error messages
     */
    protected $messages = array();

    /**
     * @var int                   number of errors (invalids)
     */
    protected $err_num = 0;

    /**
     * @var Verify
     */
    public $validate = null;

    // +----------------------------------------------------------------------+
    /**
     * @Inject
     * @param \WScore\Validation\Verify   $validate
     */
    public function __construct( $validate )
    {
        $this->validate = $validate;
    }

    /**
     * @param array $data
     */
    public function source( $data=array() )
    {
        $this->source = $data;
    }

    // +----------------------------------------------------------------------+
    //  getting found values
    // +----------------------------------------------------------------------+
    /**
     * returns found value.
     * this method returns values that maybe invalid.
     *
     * @param null|string $key
     * @return array
     */
    public function get( $key=null )
    {
        if( $key ) return Utils\Helper::arrGet( $this->found, $key );
        return $this->found;
    }

    /**
     * returns all the valid values.
     *
     * @return array
     */
    public function getSafe()
    {
        $safeData = $this->found;
        $this->_findClean( $safeData, $this->messages );
        return $safeData;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Dio
     */
    public function set( $name, $value )
    {
        $this->found[ $name ] = $value;
        return $this;
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

    // +----------------------------------------------------------------------+
    //  errors and messages
    // +----------------------------------------------------------------------+
    /**
     * @return bool
     */
    public function fails()
    {
        return $this->err_num?true:false;
    }

    /**
     * @param null|string $name
     * @return array|mixed
     */
    public function message( $name=null )
    {
        if( $name ) return Utils\Helper::arrGet( $this->messages, $name );
        return $this->messages;
    }

    /**
     * @param string $name
     * @param mixed $error
     * @param bool|mixed $value
     * @return Dio
     */
    public function isError( $name, $error, $value=false )
    {
        $this->messages[ $name ] = $error;
        if( $value !== false ) $this->set( $name, $value );
        $this->err_num ++;
        return $this;
    }

    // +----------------------------------------------------------------------+
    //  find and validate and save it to found
    // +----------------------------------------------------------------------+
    /**
     * @param string      $value
     * @param Rules|array $rules
     * @return bool|string
     */
    public function verify( $value, $rules )
    {
        return $this->validate->is( $value, $rules );
    }

    /**
     * @return Utils\ValueToInterface
     */
    public function result() {
        return $this->validate->result();
    }

    /**
     * pushes the $name.
     * returns the found value, or false if validation fails.
     *
     * @param string $name
     * @param array|Rules $rules
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function is( $name, $rules )
    {
        if( !is_string($name) ) {
            throw new \InvalidArgumentException( "name must be a string" );
        }
        $found = $this->find( $name, $rules );
        $this->validate->is( $found, $rules );

        $valTO = $this->validate->result();
        if( $valTO->fails() ) {
            $found = $valTO->getValue();
            $message = $valTO->message();
            $this->isError( $name, $message, $found );
            if( is_array( $found ) ) {
                $this->_findClean( $found, $message );
                return $found;
            }
            return false;
        }
        $this->set( $name, $valTO->getValue() );
        return $valTO->getValue();
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
        if( Utils\Helper::arrGet( $rules, 'multiple' ) ) {
            // check for multiple case i.e. Y-m-d.
            $value = Utils\Helper::prepare_multiple( $name, $this->source, $rules[ 'multiple' ] );
        }
        if( !$value && array_key_exists( $name, $this->source ) ) {
            // simplest case.
            $value = $this->source[ $name ];
        }
        // prepares filter for sameWith.
        $rules = Utils\Helper::prepare_sameWith( $this, $rules );
        return $value;
    }
    // +----------------------------------------------------------------------+
}