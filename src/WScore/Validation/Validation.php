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
    public static function factory( $locale=null )
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
    private function _findClean( &$data, $error )
    {
        if( empty( $error ) ) return; // no error at all.
        foreach( $data as $key => $val ) {
            if( !array_key_exists( $key, $error ) ) {
                continue; // no error.
            }
            if( is_array( $data[ $key ] ) && is_array( $error[ $key ] ) ) {
                $this->_findClean( $data[$key], $error[$key] );
            }
            else {
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
     * @param Rules $rules
     * @return mixed
     */
    public function push( $name, $rules )
    {
        $found = $this->find( $name, $rules );
        if( !$this->keep( $found, $name ) ) {
            return false;
        }
        return $this->output[ $name ];
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
     * @param Rules  $rules
     * @return ValueTO|ValueTO[]
     */
    public function find( $name, $rules )
    {
        // find a value from data source.
        $value = null;
        if( array_key_exists( $name, $this->source ) ) {
            // simplest case.
            $value = $this->source[ $name ];
        }
        elseif( Utils::arrGet( $rules, 'multiple' ) ) {
            // check for multiple case i.e. Y-m-d.
            $value = Utils::prepare_multiple( $name, $this->source, $rules[ 'multiple' ] );
        }
        // prepares filter for sameWith.
        $rules = Utils::prepare_sameWith( $this, $rules );

        // now, validate this value.
        if( is_array( $value ) ) {
            $found = array();
            foreach( $value as $key => $val ) {
                $found[ $key ] = $this->validate->is( $val, $rules );
            }
        } else {
            $found = $this->validate->is( $value, $rules );
        }
        return $found;
    }

    /**
     * @param ValueTO|ValueTO[] $found
     * @param string $key
     * @param null $key2
     * @return bool
     */
    private function keep( $found, $key, $key2=null )
    {
        $isValid = true;
        if( is_array( $found ) ) {

            /** @var $found ValueTO[] */
            foreach( $found as $k => $f ) {
                $isValid &= $this->keep( $f, $key, $k );
            }
            return $isValid;

        } else {

            /** @var $found ValueTO */
            if( $found->getError() ) {

                $isValid = false;
                if( $key2 !== null ) {
                    $this->errors[ $key ][ $key2 ] = $found->getMessage();
                } else {
                    $this->errors[ $key ] = $found->getMessage();
                }
                $this->err_num ++;
            }
            if( $key2 !== null ) {
                $this->output[ $key ][ $key2 ] = $found->getValue();
            } else {
                $this->output[ $key ] = $found->getValue();
            }
        }
        return $isValid;
    }
    // +----------------------------------------------------------------------+
}