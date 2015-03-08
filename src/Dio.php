<?php
namespace WScore\Validation;

/**
 * Class Dio
 *
 * @package WScore\Validation
 *
 * Data Import Object
 * for validating a a data, an array of values (i.e. input from html form).
 *
 * @method Rules asText(string $key)
 * @method Rules asMail(string $key)
 * @method Rules asBinary(string $key)
 * @method Rules asNumber(string $key)
 * @method Rules asInteger(string $key)
 * @method Rules asFloat(string $key)
 * @method Rules asDate(string $key)
 * @method Rules asDatetime(string $key)
 * @method Rules asDateYM(string $key)
 * @method Rules asTime(string $key)
 * @method Rules asTimeHi(string $key)
 * @method Rules asTel(string $key)
 */
class Dio
{
    /**
     * @var array                 source of data to read from
     */
    protected $source = array();

    /**
     * @var Rules[]
     */
    protected $rules = [];

    /**
     * @var array                 validated and invalidated data
     */
    protected $found = array();

    /**
     * @var array                 invalidated error messages
     */
    protected $messages = array();

    /**
     * @var int                   number of errors (invalids)
     */
    protected $err_num = 0;

    /**
     * @var Verify
     */
    public $verify = null;

    /**
     * @var Rules
     */
    private $ruler;

    // +----------------------------------------------------------------------+
    /**
     * @Inject
     * @param Verify $verify
     * @param Rules  $rules
     */
    public function __construct($verify, $rules)
    {
        $this->verify = $verify;
        $this->ruler  = $rules;
    }

    /**
     * @param array $data
     */
    public function source($data = array())
    {
        $this->source = $data;
    }

    /**
     * @param $name
     * @param $type
     * @return Rules
     */
    public function setRule($name, $type)
    {
        $this->rules[$name] = $this->ruler->withType($type);

        return $this->rules[$name];
    }

    /**
     * @param string $type
     * @return Rules
     */
    public function getRule($type)
    {
        return $this->ruler->withType($type);
    }

    /**
     * pushes the $name.
     * returns the found value, or false if validation fails.
     *
     * @param string      $name
     * @param array|Rules $rules
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function is($name, $rules)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException("name must be a string");
        }
        $this->rules[$name] = $rules;

        return $this->get($name);
    }

    /**
     * @param string $method
     * @param array  $args
     * @return Rules
     */
    public function __call($method, $args)
    {
        if (substr($method, 0, 2) === 'as') {
            $type = strtolower(substr($method, 2));
            $name = $args[0];

            return $this->rules[$name] = $this->getRule($type);
        }
        throw new \BadMethodCallException;
    }

    // +----------------------------------------------------------------------+
    //  getting found values
    // +----------------------------------------------------------------------+
    /**
     * returns found value.
     * this method returns values that maybe invalid.
     *
     * @param null|string $key
     * @return array
     */
    public function get($key = null)
    {
        if (!$key) {
            return $this->found;
        }
        if (array_key_exists($key, $this->found)) {
            return $this->found[$key];
        }
        $rules = array_key_exists($key, $this->rules) ? $this->rules[$key] : Rules::text();
        $found = $this->find($key, $rules);
        $valTO = $this->verify->apply($found, $rules);

        if ($valTO->fails()) {
            $found   = $valTO->getValue();
            $message = $valTO->message();
            $this->isError($key, $message, $found);
            if (is_array($found)) {
                $this->_findClean($found, $message);

                return $found;
            }

            return false;
        }
        $this->set($key, $valTO->getValue());

        return $valTO->getValue();
    }

    /**
     * returns all the valid values.
     *
     * @return array
     */
    public function getSafe()
    {
        $safeData = $this->found;
        $this->_findClean($safeData, $this->messages);

        return $safeData;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @return Dio
     */
    public function set($name, $value)
    {
        $this->found[$name] = $value;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function del($key)
    {
        if (isset($this->found[$key])) {
            unset($this->found[$key]);
        }

        return $this;
    }

    /**
     * @param array        $data
     * @param array|string $error
     */
    protected function _findClean(&$data, $error)
    {
        if (empty($error)) {
            return;
        } // no error at all.
        foreach ($data as $key => $val) {
            if (!array_key_exists($key, $error)) {
                continue; // no error.
            }
            if (is_array($data[$key]) && is_array($error[$key])) {
                $this->_findClean($data[$key], $error[$key]);
            } elseif ($error[$key]) { // error message exist.
                unset($data[$key]);
            }
        }
    }

    // +----------------------------------------------------------------------+
    //  errors and messages
    // +----------------------------------------------------------------------+
    /**
     * @return bool
     */
    public function fails()
    {
        return $this->err_num ? true : false;
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return $this->err_num ? false : true;
    }

    /**
     * @param null|string $name
     * @return array|mixed
     */
    public function message($name = null)
    {
        if ($name) {
            return Utils\Helper::arrGet($this->messages, $name);
        }

        return $this->messages;
    }

    /**
     * @param string     $name
     * @param mixed      $error
     * @param bool|mixed $value
     * @return Dio
     */
    public function isError($name, $error, $value = false)
    {
        $this->messages[$name] = $error;
        if ($value !== false) {
            $this->set($name, $value);
        }
        $this->err_num++;

        return $this;
    }

    // +----------------------------------------------------------------------+
    //  find and validate and save it to found
    // +----------------------------------------------------------------------+
    /**
     * @param string      $value
     * @param Rules|array $rules
     * @return bool|string
     */
    public function verify($value, $rules)
    {
        return $this->verify->is($value, $rules);
    }

    /**
     * finds a value with $name in the source data, applying the rules.
     *
     * @param string      $name
     * @param array|Rules $rules
     * @return string
     */
    public function find($name, &$rules = [])
    {
        // find a value from data source.
        $value = null;
        if (Utils\Helper::arrGet($rules, 'multiple')) {
            // check for multiple case i.e. Y-m-d.
            $value = Utils\Helper::prepare_multiple($name, $this->source, $rules['multiple']);
        }
        if (!$value && array_key_exists($name, $this->source)) {
            // simplest case.
            $value = $this->source[$name];
        }
        // prepares filter for sameWith.
        $rules = Utils\Helper::prepare_sameWith($this, $rules);

        return $value;
    }
    // +----------------------------------------------------------------------+
}