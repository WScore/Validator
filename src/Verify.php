<?php
namespace WScore\Validation;

/**
 * Class Validate
 * @package WScore\Validation
 */
class Verify
{
    /**
     * @var Filter
     */
    public $filter;

    /**
     * @var ValueTO
     */
    public $valueTO;

    /**
     * @var ValueTO
     */
    protected $lastValue;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param Filter  $filter
     * @param ValueTO $valueTO
     */
    public function __construct( $filter=null, $valueTO=null )
    {
        if( isset( $filter  ) ) $this->filter  = $filter;
        if( isset( $valueTO ) ) $this->valueTO = $valueTO;
    }

    // +----------------------------------------------------------------------+
    //  validation: for a single value.
    // +----------------------------------------------------------------------+
    /**
     * validates a text value, or an array of text values.
     * returns the filtered value, or false if validation fails.
     *
     * @param string $value
     * @param array  $rules
     * @return bool|mixed
     */
    public function is( $value, $rules )
    {
        // -------------------------------
        // validating a single value.
        if( !is_array( $value ) ) {
            $valTO = $this->applyFilters( $value, $rules );
            if( $valTO->fails() ) {
                return false;
            }
            return $valTO->getValue();
        }
        // -------------------------------
        // validating for an array input.
        $result = array();
        $errors = array();
        $failed = false;
        foreach( $value as $key => $val ) {
            $valTO = $this->applyFilters( $val, $rules );
            $result[$key] = $valTO->getValue();
            if( $valTO->fails() ) {
                $failed = true;
                $errors[$key] = $valTO->message();
            }
        }
        // done validation for an array.
        // hack the lastValue to have the array of result!
        $this->lastValue->setValue( $result );
        if( $failed ) {
            $this->lastValue->setMessage( $errors );
            $this->lastValue->setError( 'input=array' ); // ouch!
            return false;
        }
        return $result;
    }

    /**
     * @return ValueToInterface
     */
    public function result() {
        return $this->lastValue;
    }

    /**
     * apply filters on a single value.
     *
     * @param string $value
     * @param array $rules
     * @return null|ValueTO
     */
    public function applyFilters( $value, $rules=array() )
    {
        /** @var $filter Filter */
        $valueTO = $this->valueTO->reset( $value );
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
        $this->lastValue = $valueTO;
        return $valueTO;
    }
    // +----------------------------------------------------------------------+
}