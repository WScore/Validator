<?php
namespace WScore\Validation;

class Validate
{
    /**
     * @Inject
     * @var \WScore\Validation\Filter
     */
    protected $filter;

    /**
     * @Inject
     * @var \WScore\Validation\Message
     */
    protected $message;

    /**
     * @Injection
     * @var array|Rules
     */
    protected $rules;

    public $isValid;

    public $value;
    
    public $err_msg;

    /** @var string */
    public $userMessage;
    // +----------------------------------------------------------------------+
    /**
     * @param \WScore\Validation\Filter  $filter
     * @param \WScore\Validation\Message $message
     * @param \WScore\Validation\Rules   $rule
     */
    public function __construct( $filter=null, $message=null, $rule=null )
    {
        if( isset( $filter  ) ) $this->filter  = $filter;
        if( isset( $message ) ) $this->message = $message;
        if( isset( $rule    ) ) $this->rules   = $rule;
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
        $this->userMessage = $message;
    }

    /**
     * generates error message from filter's error information.
     *
     * @param array $error
     * @param array $rules
     * @return string
     */
    public function getMessage( $error, $rules )
    {
        if( isset( $this->userMessage ) ) return $this->userMessage;
        if( !$this->message ) return $error;
        $type = array_key_exists( 'type', $rules ) ? $rules[ 'type' ] : null;
        return $this->message->message( $error, $error['message'], $type );
    }

    // +----------------------------------------------------------------------+
    /**
     * @param string|array $value
     * @param Rules|array  $rules
     * @param null|string  $message
     * @return bool|mixed
     */
    public function is( $value, $rules, $message=null ) {
        $valid = $this->validate( $value, $rules, $message );
        if( $valid ) {
            return $this->value;
        }
        return false;
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
                $filter = $this->applyFilters( $val, $rules );
                $success = !$filter->error();
                $this->value[ $key ] = $filter->value;
                if( !$success ) {
                    $this->err_msg[ $key ] = $filter->error;
                }
                $this->isValid &= ( $success === true );
            }
            $this->isValid = (bool) $this->isValid;
            return $this->isValid;
        }
        $filter = $this->applyFilters( $value, $rules );
        $this->isValid = !$filter->error();
        $this->err_msg = $filter->error;
        $this->value   = $filter->value;
        return $this->isValid;
    }

    /**
     * do the validation for a single value.
     *
     * @param string $value
     * @param array  $rules
     * @return Filter
     */
    public function applyFilters( $value, $rules )
    {
        /** @var $filter Filter */
        $filter = $this->filter->start( $value );
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
            // loop break.
            if( $filter->breakLoop() ) break;
        }
        // got some error.
        if( $error = $filter->error ) {
            $filter->error = $this->getMessage( $error, $rules );
        }
        return $filter;
    }
    // +----------------------------------------------------------------------+
}