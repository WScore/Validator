<?php
declare(strict_types=1);

namespace WScore\Validation;

use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Locale\TypeFilters;
use WScore\Validation\Validators\Builder;
use WScore\Validation\Validators\ValidationChain;
use WScore\Validation\Validators\ValidationList;
use WScore\Validation\Validators\ValidationRepeat;

class ValidatorBuilder
{
    /**
     * @var Messages
     */
    private $messages;

    /**
     * @var TypeFilters
     */
    private $typeFilter;

    /**
     * ValidatorBuilder constructor.
     * @param string $locale
     */
    public function __construct($locale = 'en')
    {
        $this->messages = Messages::create($locale);
        $this->typeFilter = TypeFilters::create($locale);
    }

    /**
     * @param array $options
     * @return ValidationInterface
     */
    public function __invoke(array $options = []): ValidationInterface
    {
        return Builder::forge($this->messages, $this->typeFilter, $options);
    }

    /**
     * @param array $options
     * @return ValidationList
     */
    public function form(array $options = []): ValidationInterface
    {
        $options['type'] = 'form';
        return Builder::forge($this->messages, $this->typeFilter, $options);
    }

    /**
     * @param array $options
     * @return ValidationRepeat
     */
    public function repeat(array $options = []): ValidationInterface
    {
        $options['type'] = 'repeat';
        return Builder::forge($this->messages, $this->typeFilter, $options);
    }

    /**
     * @param array $options
     * @return ValidationChain|ValidationRepeat
     */
    public function chain(array $options = []): ValidationInterface
    {
        return Builder::forge($this->messages, $this->typeFilter, $options);
    }

    private function buildType(array $options, string $type)
    {
        $options['type'] = $type;
        return Builder::forge($this->messages, $this->typeFilter, $options);
    }

    /**
     * @param array $options
     * @return ValidationChain|ValidationRepeat
     */
    public function text(array $options = []): ValidationInterface
    {
        return $this->buildType($options, 'text');
    }

    /**
     * @param array $options
     * @return ValidationChain|ValidationRepeat
     */
    public function email(array $options = []): ValidationInterface
    {
        return $this->buildType($options, 'email');
    }

    /**
     * @param array $options
     * @return ValidationChain|ValidationRepeat
     */
    public function integer(array $options = []): ValidationInterface
    {
        return $this->buildType($options, 'integer');
    }

    /**
     * @param array $options
     * @return ValidationChain|ValidationRepeat
     */
    public function date(array $options = []): ValidationInterface
    {
        return $this->buildType($options, 'date');
    }

    public function digits(array $options = []): ValidationInterface
    {
        return $this->buildType($options, 'digits');
    }
}