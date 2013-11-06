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
        if( $method === false ) {
            $this->error = $method;
            return;
        }
        $this->error = array(
            'method' => $method,
            'parameter' => $p,
            $method => $p,
        );
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
     * @return mixed
     */
    public function  __toString() 
    {
        return $this->value;
    }

}