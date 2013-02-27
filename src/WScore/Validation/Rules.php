<?php
namespace WScore\Validation;

/**
 * about pattern and matches filter.
 * both filters uses preg_match for patter match.
 * it's just pattern is uses in html5's form element, while matches are not.
 *
 */

/** 
 * @method text() 
 * @method mail()
 * @method number()
 * @method integer()
 * @method float()
 * @method date()
 * @method dateYM()
 * @method time()
 * @method timeHi()
 * @method tel()
 * @method fax()
 */
class Rules implements \ArrayAccess
{
    /** @var array        order of filterOptions to apply     */
    protected $filterOrder = array();

    /** @var array        predefined filter filter set        */
    public $filterTypes = array();

    /** @var null|string */
    public $type = null;

    /** @var array */
    public $filter = array();
    
    // +----------------------------------------------------------------------+
    /**
     */
    public function __construct()
    {
        // define order of filterOptions when applying. order can be critical when
        // modifying the string (such as capitalize before checking patterns).
        //   rule => option
        // if option is FALSE, the rule is skipped.
        $this->filterOrder = array(
            // filterOptions (modifies the value)
            'type'        => null,       // type of filter, such as 'text', 'mail', etc.
            'err_msg'     => false,
            'message'     => false,
            'multiple'    => false,      // multiple value.
            'noNull'      => true,       // filters out NULL (\0) char from the value.
            'encoding'    => 'UTF-8',    // checks the encoding of value.
            'mbConvert'   => 'standard', // converts Kana set (Japanese)
            'trim'        => true,       // trims value.
            'sanitize'    => false,      // done, kind of
            'string'      => false,      // converts value to upper/lower/etc.
            'default'     => '',         // sets default if value is empty.
            // validators (only checks the value).
            'required'    => false,      // fails if value is empty.
            'loopBreak'   => true,       // done, skip validations if value is empty.
            'code'        => false,
            'maxlength'   => false,
            'pattern'     => false,      // checks pattern with preg_match.
            'matches'     => false,      // preg_match with default types.
            'min'         => false,
            'max'         => false,
            'range'       => false,
            'checkdate'   => false,
            'mbCheckKana' => false,
            'sameWith'    => false,      // comparing with other field.
            'sameAs'      => false,
            'sameEmpty'   => false,
        );

        // filters for various types of input.
        $this->filterTypes = array(
            'binary'   => 'noNull:FALSE | encoding:FALSE | mbConvert:FALSE | trim:FALSE ',
            'text'     => '',
            'mail'     => 'mbConvert:hankaku | sanitize:mail | matches:mail',
            'number'   => 'mbConvert:hankaku | matches:number',
            'integer'  => 'mbConvert:hankaku | matches:int',
            'float'    => 'mbConvert:hankaku | matches:float',
            'date'     => 'multiple:YMD | mbConvert:hankaku | pattern:[0-9]{4}-[0-9]{2}-[0-9]{2}',
            'dateYM'   => 'multiple:YM  | mbConvert:hankaku | pattern:[0-9]{4}-[0-9]{2}',
            'time'     => 'multiple:His | mbConvert:hankaku | pattern:[0-9]{2}:[0-9]{2}:[0-9]{2}',
            'timeHi'   => 'multiple:Hi  | mbConvert:hankaku | pattern:[0-9]{2}:[0-9]{2}',
            'tel'      => 'multiple:tel | mbConvert:hankaku',
            'fax'      => 'multiple:tel | mbConvert:hankaku',
        );
        
        // default filter is filterOrder. 
        $this->filter = $this->filterOrder;
    }

    // +----------------------------------------------------------------------+
    /**
     * @return null|string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isRequired() {
        return !!$this->filter[ 'required' ];
    }

    /**
     * @return mixed
     */
    public function getPattern() {
        return $this->filter[ 'pattern' ];
    }

    public function getFilters( $name=null ) {
        if( isset( $name ) ) { 
            if( array_key_exists( $name, $this->filter ) ) return $this->filter[ $name ]; 
            return null;
        }
        return $this->filter;
    }
    // +----------------------------------------------------------------------+
    /**
     * @param $type
     * @param $filters
     * @return Rules
     */
    public function ruleForType( $type, $filters )
    {
        $rule = new static();
        $rule->type = $type;
        $typeFilter = $this->filterTypes[ $type ];
        $rule->mergeFilter( $typeFilter );
        $rule->mergeFilter( $filters );
        $this->filter[ 'type' ] = $type;
        return $rule;
    }

    /**
     * @param null|string|array $filters
     * @return Rules
     */
    public function email( $filters=null ) {
        return $this->ruleForType( 'mail', $filters );
    }

    /**
     * @param string $method
     * @param mixed $args
     * @return Rules
     */
    public function __call( $method, $args ) {
        $filter = array_key_exists( 0, $args ) ? $args[0]: null;
        return $this->ruleForType( $method, $filter );
    }
    // +----------------------------------------------------------------------+
    //  tools for filters. 
    // +----------------------------------------------------------------------+
    /**
     * merges text/array filters into Rule object's filter. 
     * 
     * @param array $filter ,
     * @return array
     */
    public function mergeFilter( $filter )
    {
        if( is_string( $filter ) ) {
            $filter = Utils::convertFilter( $filter );
        }
        if( empty( $filter ) ) return;
        foreach( $filter as $key => $val ) {
            $this->filter[ $key ] = $val;
        }
        return;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset   An offset to check for.
     * @return boolean true on success or false on failure.
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists( $offset ) {
        return array_key_exists( $offset, $this->filter );
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset   The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset ) {
        return array_key_exists( $offset, $this->filter )? $this->filter[ $offset ] : null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset   The offset to assign the value to.
     * @param mixed $value    The value to set.
     * @return void
     */
    public function offsetSet( $offset, $value ) {
        $this->filter[ $offset ] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset   The offset to unset.
     * @return void
     */
    public function offsetUnset( $offset ) {
        if( array_key_exists( $offset, $this->filter ) ) unset( $this->filter[ $offset ] );
    }
}