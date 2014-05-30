<?php
namespace WScore\Validation;

class Validate
{
    /**
     * @Inject
     * @var Filter
     */
    public $filter;

    /**
     * @Inject
     * @var ValueTO
     */
    public $valueTO;

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

    /**
     * @param null|string $locale
     * @return static
     */
    public static function getInstance( $locale=null )
    {
        return new static(
            new Filter(), new ValueTO( Message::getInstance( $locale ) )
        );
    }

    /**
     * validates a single or array of text value(s).
     * returns the filtered value(s) and errors are
     * returned as 3rd argument.
     *
     * @param string|array $value
     * @param array|Rules $rules
     * @param string|array $errors
     * @return array|mixed
     */
    public function verify( $value, $rules, &$errors )
    {
        if( is_array( $value ) ) {
            $result = array();
            $errors = array();
            foreach( $value as $k => $v ) {
                $errors[$k] = null;
                $result[$k] = $this->verify( $v, $rules, $errors[$k] );
                if( !$errors[$k] ) {
                    unset( $errors[$k] );
                }
            }
            return $result;
        }
        $valTO = $this->applyFilters( $value, $rules );
        if( $valTO->getError() ) {
            $errors = $valTO->getMessage();
        }
        return $valTO->getValue();
    }

    /**
     * validates a single text value.
     * returns the filtered value, or false if validation fails.
     *
     * @param string $value
     * @param array  $rules
     * @param null|string  $message
     * @return bool|mixed
     */
    public function is( $value, $rules, $message=null ) 
    {
        if( $message ) $rules['message'] = $message;
        $this->applyFilters( $value, $rules );
        if( !$this->valueTO->getError() ) {
            return $this->valueTO->getValue();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isValid() {
        if( $this->valueTO->getError() ) {
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->valueTO->getValue();
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        if( $this->valueTO->getError() ) {
            return $this->valueTO->getMessage();
        }
        return null;
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
        $this->valueTO->reset( $value );
        // loop through all the rules to validate $value.
        foreach( $rules as $rule => $parameter )
        {
            // some filters are not to be applied...
            if( $parameter === false ) continue; // skip rules with option as FALSE.
            // apply filter.
            $method = 'filter_' . $rule;
            if( method_exists( $this->filter, $method ) ) {
                $this->filter->$method( $this->valueTO, $parameter );
            } elseif( is_object( $parameter ) && is_callable( $parameter ) ) {
                $this->filter->applyClosure( $this->valueTO, $parameter );
            }
            // loop break.
            if( $this->valueTO->getBreak() ) break;
        }
        return $this->valueTO;
    }
    // +----------------------------------------------------------------------+
}