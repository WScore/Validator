<?php
namespace WScore\Validation;

use WScore\Validation\Locale\String as Locale;

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
 * @method Rules mbCheckKana( string $type )
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
     * @Inject
     * @var Rules
     */
    protected static $_rules;

    /**
     * @Inject
     * @var Locale
     */
    public $locale = 'en';

    // +----------------------------------------------------------------------+
    //  managing object
    // +----------------------------------------------------------------------+
    /**
     * @param string|null $locale
     */
    public function __construct( $locale=null )
    {
        if( $locale ) $this->locale = $locale;
        $this->loadFilter();
        $this->loadFilterType();
        static::$_rules = $this;
    }

    /**
     * @param null|string $filename
     */
    public function loadFilter( $filename=null )
    {
        if( !$filename ) {
            $locale = strtolower( locale_get_primary_language( $this->locale ) );
            $filename = __DIR__ . "/Locale/Filter.{$locale}.php";
        }
        /** @noinspection PhpIncludeInspection */
        $this->baseFilters = include( $filename );
        $this->filter      = $this->baseFilters;
    }

    /**
     * @param null|string $filename
     */
    public function loadFilterType( $filename=null )
    {
        if( !$filename ) {
            $locale = strtolower( locale_get_primary_language( $this->locale ) );
            $filename = __DIR__ . "/Locale/FilterType.{$locale}.php";
        }
        /** @noinspection PhpIncludeInspection */
        $this->filterTypes = include( $filename );
    }
    
    /**
     * @param string $type
     * @param string $text
     * @return Rules
     */
    public function __invoke( $type, $text='' )
    {
        $this->applyType( $type );
        if( $text ) {
            $this->applyTextFilter( $text );
        }
        return $this;
    }

    /**
     * @param null|string|Locale $locale
     * @return static
     */
    public static function getInstance( $locale=null )
    {
        return new static( $locale );
    }

    /**
     * @param $method
     * @param $args
     * @return \WScore\Validation\Rules
     */
    public static function __callStatic( $method, $args )
    {
        if( !static::$_rules ) {
            static::getInstance();
        }
        static::$_rules->applyType( $method );
        foreach( $args as $arg ) {
            static::$_rules->apply( $arg );
        }
        return static::$_rules;
    }
    // +----------------------------------------------------------------------+
    //  setting rule
    // +----------------------------------------------------------------------+
    /**
     * @param string $type
     * @throws \RuntimeException
     */
    public function applyType( $type )
    {
        $type = strtolower( $type );
        if( $type == 'email' ) $type = 'mail';
        if( !array_key_exists( $type, $this->filterTypes ) ) {
            throw new \RuntimeException( "rule type not defined: {$type}" );
        }
        $this->filter = array_merge( $this->baseFilters, $this->filterTypes[ $type ] );
        $this->filter[ 'type' ] = $type;
    }

    /**
     * @param string $type
     * @param mixed $filter
     * @return $this
     */
    public function addFilterType( $type, $filter )
    {
        $this->filterTypes[ $type ] = $filter;
        return $this;
    }

    /**
     * @param array|string $filters
     * @return $this
     * @throws \RuntimeException
     */
    public function apply( $filters )
    {
        if( is_array( $filters ) ) {
            return $this->applyFilters( $filters );
        }
        elseif( is_string( $filters ) ) {
            return $this->applyTextFilter( $filters );
        }
        throw new \RuntimeException( "filters must be an array or a text string. " );
    }

    /**
     * @param $text
     * @return $this
     */
    public function applyTextFilter( $text )
    {
        $filter = Utils::convertFilter( $text );
        if( empty( $filter ) ) return $this;
        foreach( $filter as $key => $val ) {
            $this->filter[ $key ] = $val;
        }
        return $this;
    }

    /**
     * @param $filters
     * @return $this
     */
    public function applyFilters( $filters )
    {
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
     * returns a list of available types. 
     * 
     * @return array
     */
    public function getTypeList()
    {
        return array_keys( $this->filterTypes );
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
     * @param null|string $name
     * @return array|null
     */
    public function getFilters( $name=null ) {
        if( isset( $name ) ) { 
            if( array_key_exists( $name, $this->filter ) ) return $this->filter[ $name ]; 
            return null;
        }
        return $this->filter;
    }
    // +----------------------------------------------------------------------+
    //  tools for filters.
    // +----------------------------------------------------------------------+
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

    /**
     * Retrieve an external iterator
     * @return Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator( $this->filter );
    }
}