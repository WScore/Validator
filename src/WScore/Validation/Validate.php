<?php
namespace WScore\Validation;

class Validate
{
    /** @var \WScore\Validation\Filter */
    protected $filter;

    /** @var Message */
    protected $message;

    /** @var array|Rules */
    protected $rules;

    public $isValid;

    public $value;
    
    public $err_msg;

    // +----------------------------------------------------------------------+
    /**
     * @param \WScore\Validation\Filter  $filter
     * @param \WScore\Validation\Message $message
     * @DimInjection Get \WScore\Validation\Filter
     * @DimInjection Get \WScore\Validation\Message
     */
    public function __construct( $filter, $message )
    {
        $this->filter  = $filter;
        $this->message = $message;
    }

    /**
     * initializes internal values.
     *
     * @param array       $rules
     * @param null|string $message
     */
    protected function init( $rules, $message=null )
    {
        $this->rules   = $rules;
        $this->value   = null;
        $this->isValid = true;
        $this->err_msg = null;
        $this->message->setMessage( $message );
    }

    /**
     * generates error message from filter's error information.
     *
     * @param array $error
     * @return string
     */
    public function getMessage( $error )
    {
        if( !$this->message ) return $error;
        $type = array_key_exists( 'type', $this->rules ) ? $this->rules[ 'type' ] : null;
        return $this->message->message( $error, $this->filter->err_msg, $type );
    }

    // +----------------------------------------------------------------------+
    /**
     * @param string|array $value
     * @param Rules|array  $rules
     * @param null|string  $message
     * @return bool
     */
    public function is( $value, $rules, $message=null ) {
        return $this->validate( $value, $rules, $message );
    }
    /**
     * validates a value or an array of values for a given filters.
     * filter must be an array.
     *
     * @param string|array $value
     * @param Rules|array  $rules
     * @param null|string  $message
     * @return bool
     */
    public function validate( $value, $rules, $message=null )
    {
        if( $rules instanceof Rules ) $rules = $rules->getFilters();
        $this->init( $rules, $message );
        if( is_array( $value ) )
        {
            $this->value   = array();
            $this->err_msg = array();
            foreach( $value as $key => $val ) 
            {
                $success = $this->applyFilters( $val, $this->filter, $rules );
                $this->value[ $key ] = $this->filter->value;
                if( !$success ) {
                    $this->err_msg[ $key ] = $this->filter->error;
                }
                $this->isValid &= ( $success === true );
            }
            $this->isValid = (bool) $this->isValid;
            return $this->isValid;
        }
        $this->isValid = $this->applyFilters( $value, $this->filter, $rules );
        $this->err_msg = $this->filter->error;
        $this->value   = $this->filter->value;
        return $this->isValid;
    }

    /**
     * do the validation for a single value.
     *
     * @param string $value
     * @param Filter $filter
     * @param array  $rules
     * @return bool
     */
    public function applyFilters( $value, $filter, $rules )
    {
        $filter->setup( $value );
        $success = true;
        // loop through all the rules to validate $value.
        foreach( $rules as $rule => $parameter )
        {
            // some filters are not to be applied...
            if( $parameter === false ) continue; // skip rules with option as FALSE.
            // apply filter.
            $method = 'filter_' . $rule;
            if( method_exists( $filter, $method ) ) {
                $filter->$method( $parameter );
            }
            // got some error. 
            if( $filter->error ) {
                $filter->error = $this->getMessage( $filter->error );
                $success = false;
                break;
            }
            // loop break. 
            if( $filter->break ) break;
        }
        return $success;
    }
    // +----------------------------------------------------------------------+
}