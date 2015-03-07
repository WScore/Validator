<?php
namespace WScore\Validation;

use WScore\Validation\Utils\Filter;
use WScore\Validation\Utils\Message;
use WScore\Validation\Utils\ValueTO;

class ValidationFactory
{
    private $locale = 'en';

    private $dir = __DIR__ . '/Locale/';

    /**
     * @var Rules
     */
    private $rules;

    /**
     * @var Verify
     */
    private $verify;

    /**
     * @var Dio
     */
    private $dio;

    /**
     * @param null $locale
     * @param null $dir
     */
    public function __construct($locale = null, $dir = null)
    {
        if($locale) {
            $this->setLocale($locale, $dir);
        }
    }

    public function setLocale($locale=null, $dir = null)
    {
        $this->rules  = new Rules($locale, $dir);
        $this->verify = new Verify(
            new Filter(), 
            new ValueTO(new Message($locale, $dir))
        );
        $this->dio    = new Dio($this->verify);
    }
}