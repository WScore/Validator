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
     * @Inject
     * @var \WScore\Validation\Message
     */
    public $message;

    /**
     * last ValueTO from applyFilter
     * 
     * @var \WScore\Validation\ValueTO
     */
    public $lastValue;

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
     * @param null|string $locale
     * @return static
     */
    public static function factory( $locale=null )
    {
        return new static(
            new Filter(), new ValueTO(), new Message( $locale )
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
        $this->lastValue = $this->applyFilters( $value, $rules );
        if( !$this->lastValue->getError() ) {
            return $this->lastValue->getValue();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isValid() {
        if( $this->lastValue && $this->lastValue->getError() ) {
            return false;
        }
        return true;
    }
    /**
     * @return null|string
     */
    public function getMessage()
    {
        if( $this->lastValue && $this->lastValue->getError() ) {
            return $this->lastValue->getMessage();
        }
        return null;
    }

    /**
     * apply filters on a single value.
     *
     * @param string $value
     * @param array|Rules  $rules
     * @return ValueTO
     */
    public function applyFilters( $value, $rules=array() )
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
            } elseif( is_object( $parameter ) && is_callable( $parameter ) ) {
                $this->filter->applyClosure( $valueTO, $parameter );
            }
            // loop break.
            if( $valueTO->getBreak() ) break;
        }
        $this->message->set( $valueTO );
        return $valueTO;
    }
    // +----------------------------------------------------------------------+
}