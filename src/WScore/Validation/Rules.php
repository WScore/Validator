<?php
namespace WScore\Validation;

use WScore\DiContainer\Types\String as Locale;

/**
 * about pattern and matches filter.
 * both filters uses preg_match for patter match.
 * it's just pattern is uses in html5's form element, while matches are not.
 *
 */
use Traversable;

/** 
 * @method Rules err_msg( string $error_message )
 * @method Rules message( string $message )
 * @method Rules multiple( array $parameter )
 * @method Rules noNull( bool $not=true )
 * @method Rules encoding( string $encoding )
 * @method Rules mbConvert( string $type )
 * @method Rules trim( bool $trim=true )
 * @method Rules sanitize( string $type )
 * @method Rules string( string $type )
 * @method Rules default( string $value )
 * @method Rules required( bool $required=true )
 * @method Rules loopBreak( bool $break=true )
 * @method Rules code( string $type )
 * @method Rules maxlength( int $length )
 * @method Rules pattern( string $reg_expression )
 * @method Rules matches( string $match_type )
 * @method Rules kanaType( string $match_type )
 * @method Rules min( int $min )
 * @method Rules max( int $max )
 * @method Rules range( array $range )
 * @method Rules checkdate( bool $check=true )
 * @method Rules mbCheckKana( string $type )
 * @method Rules sameWith( string $name )
 * @method Rules sameAs( string $name )
 * @method Rules sameEmpty( bool $check=true )
 */
class Rules implements \ArrayAccess, \IteratorAggregate
{
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
     * todo: keep these filter arrays in some language files.
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
        $this->filter = include( $filename );
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
        $this->filterTypes = include( $filename );
    }
    
    /**
     * @param string $type
     * @param string $text
     * @return Rules
     */
    public function __invoke( $type, $text='' )
    {
        $rule = clone( static::$_rules );
        $rule->applyType( $type );
        if( $text ) {
            $rule->applyTextFilter( $text );
        }
        return $rule;
    }

    /**
     * @param string $type
     * @param string $text
     * @return Rules
     */
    public static function parse( $type, $text='' )
    {
        if( !static::$_rules ) {
            $rule = new static();
        } else {
            $rule = clone( static::$_rules );
        }
        $rule->applyType( $type );
        if( $text ) {
            $rule->applyTextFilter( $text );
        }
        return $rule;
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
        $this->filter[ 'type' ] = $type;
        $filters = $this->filterTypes[ $type ];
        foreach( $filters as $rule => $value ) {
            $this->$rule( $value );
        }
    }
    
    public function addFilterType( $type, $filter )
    {
        $this->filterTypes[ $type ] = $filter;
        return $this;
    }

    /**
     * @param $text
     */
    public function applyTextFilter( $text )
    {
        $filter = Utils::convertFilter( $text );
        if( empty( $filter ) ) return;
        foreach( $filter as $key => $val ) {
            $this->filter[ $key ] = $val;
        }
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