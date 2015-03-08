<?php
namespace WScore\Validation\Utils;

class Message
{
    /**
     * @var array
     */
    public $messages = array();

    // +----------------------------------------------------------------------+
    /**
     * @param null $locale
     * @param null $dir
     * @return Message
     */
    public function __construct($locale = null, $dir = null)
    {
        if (!$locale) {
            $locale = 'en';
        }
        if (!$dir) {
            $dir = dirname(__DIR__) . '/Locale/';
        }
        $dir .= $locale . '/';

        /** @noinspection PhpIncludeInspection */
        $this->setMessages(include($dir . "validation.messages.php"));
    }

    /**
     * @param $messages
     */
    protected function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * find messages based on error type.
     * 1. use message for a method/parameter set.
     * 2. use message for a specific method.
     * 3. use message for a type.
     * 4. use general error message.
     *
     * @param $type
     * @param $method
     * @param $parameter
     * @return string
     */
    public function find($type, $method, $parameter)
    {
        if (strpos($method, '::filter_') !== false) {
            $method = substr($method, strpos($method, '::filter_') + 9);
        }
        if (isset($this->messages[$method])) {
            // 1. use message for a specific method.
            if (!is_array($this->messages[$method])) {
                return $this->messages[$method];
            }
            // 2. use message for a method/parameter set.
            if (isset($this->messages[$method][$parameter])) {
                return $this->messages[$method][$parameter];
            }
        }
        if (isset($this->messages['_type_'][$type])) {
            return $this->messages['_type_'][$type];
        }

        // 4. use general error message.
        return $this->messages[0];
    }
    // +----------------------------------------------------------------------+
}