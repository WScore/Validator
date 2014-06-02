<?php
namespace WScore\Validation;

/**
 * about pattern and matches filter.
 * both filters uses preg_match for patter match.
 * it's just pattern is uses in html5's form element, while matches are not.
 *
 */
use Traversable;

/** 
 * @method Rules err_msg(   string $error_message )
 * @method Rules message(   string $message )
 * @method Rules multiple(  array $parameter )
 * @method Rules noNull(    bool $not=true )
 * @method Rules encoding(  string $encoding )
 * @method Rules mbConvert( string $type )
 * @method Rules trim( bool $trim=true )
 * @method Rules sanitize(  string $type )
 * @method Rules string(    string $type )
 * @method Rules custom(    \Closure $filter)
 * @method Rules custom2(   \Closure $filter)
 * @method Rules custom3(   \Closure $filter)
 * @method Rules default(   string $value )
 * @method Rules required(  bool $required=true )
 * @method Rules loopBreak( bool $break=true )
 * @method Rules code(      string $type )
 * @method Rules maxlength( int $length )
 * @method Rules pattern(   string $reg_expression )
 * @method Rules matches(   string $match_type )
 * @method Rules kanaType(  string $match_type )
 * @method Rules min(       int $min )
 * @method Rules max(       int $max )
 * @method Rules range(     array $range )
 * @method Rules checkdate( bool $check=true )
 * @method Rules sameWith(  string $name )
 * @method Rules sameAs(    string $name )
 * @method Rules sameEmpty( bool $check=true )
 *
 * below are static methods for types.
 *
 * @method static Rules text(    array $filters=array() )
 * @method static Rules mail(    array $filters=array() )
 * @method static Rules binary(  array $filters=array() )
 * @method static Rules number(  array $filters=array() )
 * @method static Rules integer( array $filters=array() )
 * @method static Rules float(   array $filters=array() )
 * @method static Rules date(    array $filters=array() )
 * @method static Rules datetime(array $filters=array() )
 * @method static Rules dateYM(  array $filters=array() )
 * @method static Rules time(    array $filters=array() )
 * @method static Rules timeHi(  array $filters=array() )
 * @method static Rules tel(     array $filters=array() )
 */
class Rules implements \ArrayAccess, \IteratorAggregate
{
    /**
     * this is the mother of $filter.
     * @var array
     */
    protected $baseFilters = array();

    /**
     * @var array        predefined filter filter set
     */
    protected $filterTypes = array();

    /**
     * @var array
     */
    protected $filter = array();

    /**
     * @var Rules
     */
    protected static $_rules;

    /**
     * @var string
     */
    protected static $locale = 'en';

    /**
     * @var string
     */
    protected static $dir;

    // +----------------------------------------------------------------------+
    //  managing object
    // +----------------------------------------------------------------------+
    /**
     * @param null|string $locale
     * @param null $dir
     */
    public function __construct( $locale=null, $dir=null )
    {
        if( !$locale ) $locale = static::$locale;
        if( !$dir ) {
            $dir = static::$dir ?: __DIR__ . '/Locale/';
        }
        $dir .= $locale . '/';
        /** @noinspection PhpIncludeInspection */
        $this->setFilter( include($dir."validation.filters.php" ) );
        /** @noinspection PhpIncludeInspection */
        $this->setTypes( include($dir."validation.types.php" ) );
        static::$_rules = $this;
    }

    /**
     * @param array $filters
     */
    protected function setFilter( $filters )
    {
        $this->baseFilters = $filters;
        $this->filter      = $filters;
    }

    /**
     * @param array $types
     */
    protected function setTypes( $types )
    {
        $this->filterTypes = $types;
    }

    /**
     * @param string $locale
     * @param null   $dir
     * @return string
     */
    public static function locale( $locale=null, $dir=null )
    {
        if( func_num_args() > 1 ) {
            static::$dir = func_get_arg(1); // second one 
        }
        if( !$locale ) return static::$locale;
        static::$locale = strtolower( locale_get_primary_language( $locale ) );
        return static::$locale;
    }

    /**
     * @param $method
     * @param $args
     * @return Rules
     */
    public static function __callStatic( $method, $args )
    {
        if( !static::$_rules ) {
            new static();
        }
        $rules = static::$_rules->applyType( $method );
        foreach( $args as $arg ) {
            $rules->apply( $arg );
        }
        return $rules;
    }
    // +----------------------------------------------------------------------+
    //  setting rule
    // +----------------------------------------------------------------------+
    /**
     * @param string $type
     * @return Rules|$this
     * @throws \BadMethodCallException
     */
    public function applyType( $type )
    {
        $type = strtolower( $type );
        if( $type == 'email' ) $type = 'mail';
        if( !array_key_exists( $type, $this->filterTypes ) ) {
            throw new \BadMethodCallException( "undefined type: {$type}" );
        }
        $this->filter = array_merge( $this->baseFilters, $this->filterTypes[ $type ] );
        $this->filter[ 'type' ] = $type;
        return $this;
    }

    /**
     * @param array|string $filters
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function apply( $filters )
    {
        if( is_string( $filters ) ) {
            $filters = Utils::convertFilter( $filters );
        }
        if( !is_array( $filters ) ) {
            throw new \InvalidArgumentException( "filters must be an array or a text string. " );
        }
        foreach( $filters as $rule => $parameter ) {
            if( is_numeric( $rule ) ) {
                $rule = $parameter;
                $parameter = true;
            }
            $this->filter[$rule] = $parameter;
        }
        return $this;
    }

    /**
     * @param $rule
     * @param $args
     * @return $this
     */
    public function __call( $rule, $args )
    {
        $value = isset( $args[0] ) ? $args[0] : true;
        $this->filter[ $rule ] = $value;
        return $this;
    }
    // +----------------------------------------------------------------------+
    //  getting information about Rule
    // +----------------------------------------------------------------------+
    /**
     * adds the custom filter to the rules.
     * the name, 'custom', 'custom2', and 'custom3', are reserved
     * for the filters (before the validation).
     *
     * @param string   $name
     * @param \Closure $filter
     * @return Rules
     */
    public function addCustom( $name, $filter )
    {
        $this->filter[$name] = $filter;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getType() {
        return $this->filter[ 'type' ];
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

    /**
     * @return array
     */
    public function toArray() {
        return $this->filter;
    }
    // +----------------------------------------------------------------------+
    //  for ArrayAccess and IteratorAggregate.
    // +----------------------------------------------------------------------+
    /**
     * Whether a offset exists
     * @param mixed $offset   An offset to check for.
     * @return boolean true on success or false on failure.
     */
    public function offsetExists( $offset ) {
        return array_key_exists( $offset, $this->filter );
    }

    /**
     * Offset to retrieve
     * @param mixed $offset   The offset to retrieve.
     * @return mixed Can return all value types.
     */
    public function offsetGet( $offset ) {
        return array_key_exists( $offset, $this->filter )? $this->filter[ $offset ] : null;
    }

    /**
     * Offset to set
     * @param mixed $offset   The offset to assign the value to.
     * @param mixed $value    The value to set.
     * @return void
     */
    public function offsetSet( $offset, $value ) {
        $this->filter[ $offset ] = $value;
    }

    /**
     * Offset to unset
     * @param mixed $offset   The offset to unset.
     * @return void
     */
    public function offsetUnset( $offset ) {
        if( array_key_exists( $offset, $this->filter ) ) unset( $this->filter[ $offset ] );
    }

    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->filter );
    }
}