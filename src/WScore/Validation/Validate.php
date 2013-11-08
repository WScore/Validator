<?php
namespace WScore\Validation;

class Validate
{
    /**
     * @Inject
     * @var \WScore\Validation\Filter
     */
    public $filter;

    /**
     * @Inject
     * @var \WScore\Validation\ValueTO
     */
    public $valueTO;

    /**
     * @var \WScore\Validation\Message
     */
    public $message;

    // +----------------------------------------------------------------------+
    /**
     * @param \WScore\Validation\Filter $filter
     * @param \WScore\Validation\ValueTO $valueTO
     * @param \WScore\Validation\Message $message
     */
    public function __construct( $filter=null, $valueTO=null, $message=null )
    {
        if( isset( $filter  ) ) $this->filter  = $filter;
        if( isset( $valueTO ) ) $this->valueTO = $valueTO;
        if( isset( $message ) ) $this->message = $message;
    }

    /**
     * @return static
     */
    public static function factory()
    {
        return new static(
            new Filter(), new ValueTO(), new Message()
        );
    }

    /**
     * returns the filtered value, or false if validation fails. 
     * 
     * @param string|array        $value
     * @param Rules|array         $rules
     * @return bool|mixed
     */
    public function is( $value, $rules ) 
    {
        $valueTO = $this->applyFilters( $value, $rules );
        if( !$valueTO->getError() ) {
            return $valueTO->getValue();
        }
        return false;
    }

    /**
     * apply filters on a single value.
     *
     * @param string $value
     * @param array  $rules
     * @return ValueTO
     */
    public function applyFilters( $value, $rules )
    {
        /** @var $filter Filter */
        $valueTO = $this->valueTO->forge( $value );
        // loop through all the rules to validate $value.
        foreach( $rules as $rule => $parameter )
        {
            // some filters are not to be applied...
            if( $parameter === false ) continue; // skip rules with option as FALSE.
            // apply filter.
            $method = 'filter_' . $rule;
            if( method_exists( $this->filter, $method ) ) {
                $this->filter->$method( $valueTO, $parameter );
            }
            // loop break.
            if( $valueTO->getBreak() ) break;
        }
        $this->message->set( $valueTO );
        return $valueTO;
    }
    // +----------------------------------------------------------------------+
}