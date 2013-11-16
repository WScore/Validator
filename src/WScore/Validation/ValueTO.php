<?php
namespace WScore\Validation;

/**
 * value transfer object. 
 * Class ValueTO
 *
 * @package WScore\Validation
 */
class ValueTO
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $type = 'text'; 

    /**
     * @var bool|array
     */
    private $error = false;

    /**
     * @var string
     */
    private $message;

    /**
     * @var bool
     */
    private $break = false;

    /**
     * @param $value
     * @return static
     */
    public function forge( $value )
    {
        $obj = new self;
        $obj->value = $value;
        return $obj;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue( $value )
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string      $method
     * @param null|mixed  $p
     */
    public function setError( $method, $p=null )
    {
        $this->setBreak( true );
        if( $method === false ) { // reset error to false.
            $this->error = false;
            return;
        }
        $this->error = array(
            'method' => $method,
            'parameter' => $p,
            $method => $p,
        );
    }

    /**
     * @return null|mixed
     */
    public function getParameter()
    {
        if( isset( $this->error[ 'parameter' ] ) ) {
            return $this->error[ 'parameter' ];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getErrorMethod()
    {
        if( $this->error && isset( $this->error[ 'method' ] ) ) {
            return $this->error[ 'method' ];
        }
        return null;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage( $message )
    {
        $this->message = $message;
    }

    /**
     * @return boolean
     */
    public function getBreak()
    {
        return $this->break;
    }

    /**
     * @param boolean $break
     */
    public function setBreak( $break=true )
    {
        $this->break = $break;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType( $type )
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function  __toString()
    {
        return (string) $this->value;
    }

}