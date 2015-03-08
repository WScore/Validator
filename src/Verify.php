<?php
namespace WScore\Validation;

use WScore\Validation\Utils\ValueToInterface;

/**
 * Class Validate
 * @package WScore\Validation
 */
class Verify
{
    /**
     * @var Utils\Filter
     */
    private $filter;

    /**
     * @var Utils\ValueTO
     */
    private $valueTO;

    // +----------------------------------------------------------------------+
    //  construction
    // +----------------------------------------------------------------------+
    /**
     * @param Utils\Filter  $filter
     * @param Utils\ValueTO $valueTO
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
     * @param array|Rules  $rules
     * @return bool|string
     */
    public function is( $value, $rules )
    {
        $valTO = $this->apply($value, $rules);
        if($valTO->fails()) {
            return false;
        }
        return $valTO->getValue();
    }

    /**
     * @param string $value
     * @param array|Rules  $rules
     * @return ValueToInterface
     */
    public function apply($value, $rules)
    {
        // -------------------------------
        // validating a single value.
        if( !is_array( $value ) ) {
            return $this->applyFilters( $value, $rules );
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
        $valTO = $this->valueTO->forge($result);
        if( $failed ) {
            $valTO->setMessage( $errors );
            $valTO->setError( 'input=array' ); // ouch!
        }
        return $valTO;
    }

    /**
     * apply filters on a single value.
     *
     * @param string $value
     * @param array|Rules $rules
     * @return null|Utils\ValueTO
     */
    public function applyFilters( $value, $rules=array() )
    {
        /** @var $filter Utils\Filter */
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
        return $valueTO;
    }
    // +----------------------------------------------------------------------+
}