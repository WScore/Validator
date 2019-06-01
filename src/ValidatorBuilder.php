<?php
declare(strict_types=1);

namespace WScore\Validation;

use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Locale\TypeFilters;
use WScore\Validation\Validators\ValidationChain;
use WScore\Validation\Validators\ValidationList;
use WScore\Validation\Validators\ValidationMultiple;
use WScore\Validation\Validators\ValidationRepeat;

/**
 * @method ValidationInterface text(array $options = [])
 * @method ValidationInterface email(array $options = [])
 * @method ValidationInterface digits(array $options = [])
 * @method ValidationInterface integer(array $options = [])
 * @method ValidationInterface date(array $options = [])
 */
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
        $type = $options['type'] ?? null;
        if ($type === 'form') {
            return $this->form($options);
        }
        if ($type === 'repeat') {
            return $this->repeat($options);
        }
        return $this->chain($options);
    }

    /**
     * @param array $options
     * @return ValidationList
     */
    public function form(array $options = []): ValidationInterface
    {
        $validation = new ValidationList($this->messages);
        $this::applyOptions($validation, $options);
        return $validation;
    }

    /**
     * @param array $options
     * @return ValidationRepeat
     */
    public function repeat(array $options = []): ValidationInterface
    {
        $validator = new ValidationRepeat($this->messages);
        $this::applyOptions($validator, $options);
        return $validator;
    }

    public static function applyOptions(ValidationInterface $validator, array $options): ValidationInterface
    {
        unset($options['type']);
        unset($options['multiple']);
        $message = $options['errorMessage'] ?? $options['message'] ?? null;
        if ($message && is_string($message)) {
            $validator->setErrorMessage($message);
        }
        $filters = $options['filters'] ?? [];
        unset($options['filters']);
        $filters = array_merge($filters, $options);
        $validator->addFilters($filters);

        return $validator;
    }

    /**
     * @param array $options
     * @return ValidationChain|ValidationRepeat
     */
    public function chain(array $options = []): ValidationInterface
    {
        $validator = $this->forgeValidator($options);
        $filters = $this->getFilters($options);
        $options = array_merge($filters, $options);
        return $this::applyOptions($validator, $options);
    }

    private function forgeValidator(array $options)
    {
        $multiple = $options['multiple'] ?? false;
        if ($multiple) {
            $validator = new ValidationMultiple($this->messages);;
            if (is_array($multiple)) {
                $validator->getPostFilters()->addFilters($multiple);
            }
        } else {
            $validator = new ValidationChain($this->messages);
        }

        return $validator;
    }

    private function getFilters(array $options)
    {
        $type = $options['type'] ?? null;
        if ($type) {
            return $this->typeFilter->getFilters($type);
        }
        return [];
    }

    public function __call($name, $arguments)
    {
        $options = (array) ($arguments[0] ?? []);
        $options['type'] = $name;
        return $this->chain($options);
    }
}