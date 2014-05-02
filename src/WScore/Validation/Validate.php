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

    /**
     * @Inject
     * @var Message
     */
    public $message;

    // +----------------------------------------------------------------------+
    /**
     * @param Filter  $filter
     * @param ValueTO $valueTO
     * @param Message $message
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
    public static function getInstance( $locale=null )
    {
        return new static(
            new Filter(), new ValueTO(), new Message( $locale )
        );
    }

    /**
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
        $this->message->set( $this->valueTO );
        return $this->valueTO;
    }
    // +----------------------------------------------------------------------+
}