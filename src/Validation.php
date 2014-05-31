<?php
namespace WScore\Validation;

/**
 * Class Validation
 * @package WScore\Validation
 *
 * validates an array of values (i.e. input from html form).
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
        if( $key ) return Utils::arrGet( $this->found, $key );
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
     * @param string|null $key
     * @throws \InvalidArgumentException
     * @return Validation
     */
    public function set( $name, $value, $key=null )
    {
        if( is_null($key) ) {
            $this->found[ $name ] = $value;
            return $this;
        }
        if( !isset( $this->found[$name] ) ) {
            $this->found[$name] = array();
        }
        if( !is_array( $this->found[$name] ) ) {
            throw new \InvalidArgumentException("not an array: {$name} with key:{$key}");
        }
        $this->found[$name][$key] = $value;
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
        if( $name ) return Utils::arrGet( $this->messages, $name );
        return $this->messages;
    }

    /**
     * @param string $name
     * @param mixed $error
     * @param bool|mixed $value
     * @param null $key
     * @throws \InvalidArgumentException
     * @return Validation
     */
    public function isError( $name, $error, $value=false, $key=null )
    {
        if( is_null($key) ) {
            $this->messages[ $name ] = $error;
        } else {
            if( !isset( $this->messages[$name] ) ) {
                $this->messages[$name] = array();
            }
            if( !is_array( $this->messages[$name] ) ) {
                throw new \InvalidArgumentException("not an array: {$name} with key:{$key}");
            }
            $this->messages[$name][$key] = $error;
        }
        if( $value !== false ) $this->set( $name, $value, $key );
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
     * @return ValueToInterface
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
     * @return mixed
     */
    public function is( $name, $rules )
    {
        $found = $this->find( $name, $rules );
        if( is_array( $found ) ) {
            $result = array();
            foreach( $found as $key=>$value ) {
                $valTO = $this->validate->applyFilters( $value, $rules );
                if( $valTO->fails() ) {
                    $this->isError( $name, $valTO->message(), $valTO->getValue(), $key );
                } else {
                    $this->set( $name, $valTO->getValue(), $key );
                    $result[$key] = $valTO->getValue();
                }
            }
            return $result;
        }
        $valTO = $this->validate->applyFilters( $found, $rules );
        if( $valTO->fails() ) {
            $this->isError( $name, $valTO->message(), $valTO->getValue() );
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