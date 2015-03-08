<?php
namespace WScore\Validation;

use WScore\Validation\Utils\Filter;
use WScore\Validation\Utils\Message;
use WScore\Validation\Utils\ValueTO;

class ValidationFactory
{
    /**
     * @var string      default locale.
     */
    private $locale = 'en';

    /**
     * @var string      default language directory.
     */
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
     * @param null|string $locale
     * @param null|string $dir
     */
    public function __construct($locale = null, $dir = null)
    {
        if ($locale) {
            $this->setLocale($locale, $dir);
        }
    }

    /**
     * @param null|string $locale
     * @param null|string $dir
     */
    public function setLocale($locale = null, $dir = null)
    {
        $this->locale = $locale ?: $this->locale;
        $this->dir    = $dir ?: $this->dir;

        $this->factory();
    }

    /**
     * @param array $data
     * @return Dio
     */
    public function on(array $data = [])
    {
        if (!$this->dio) {
            $this->factory();
        }
        $dio = clone($this->dio);
        $dio->source($data);

        return $dio;
    }

    /**
     * @return Verify
     */
    public function verify()
    {
        if (!$this->verify) {
            $this->factory();
        }

        return $this->verify;
    }

    private function factory()
    {
        $this->rules  = $this->rules();
        $this->verify = new Verify(
            new Filter(),
            new ValueTO(new Message($this->locale, $this->dir))
        );
        $this->dio    = new Dio($this->verify, $this->rules);
    }

    /**
     * @return Rules
     */
    public function rules()
    {
        return new Rules($this->locale, $this->dir);
    }
}