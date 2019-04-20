<?php
declare(strict_types=1);

namespace WScore\Validation\Validators;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Locale\TypeFilters;

class Builder
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
     * @var string
     */
    private $type;

    /**
     * @var bool
     */
    private $multiple = false;

    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * @param Messages $messages
     * @param TypeFilters $typeFilter
     */
    public function __construct(Messages $messages, TypeFilters $typeFilter)
    {
        $this->messages = $messages;
        $this->typeFilter = $typeFilter;
    }

    /**
     * @param Messages $messages
     * @param TypeFilters $typeFilter
     * @param array $array
     * @return ValidationInterface
     */
    public static function forge(Messages $messages, TypeFilters $typeFilter, array $array = []): ValidationInterface
    {
        $self = new self($messages, $typeFilter);
        return $self->build($array);
    }

    /**
     * @param array $array
     * @return ValidationInterface
     */
    public function build(array $array = [])
    {
        return $this->prepareOptions($array)->buildValidations();
    }

    private function prepareOptions(array $options): self
    {
        $this->type = $options['type'] ?? false;
        unset($options['type']);
        $this->multiple = $options['multiple'] ?? false;
        unset($options['multiple']);
        $filters = $options['filters']??[];
        unset($options['filters']);
        $this->filters = array_merge($filters, $options);

        return $this;
    }

    private function buildValidations(): ValidationInterface
    {
        $filters = [];
        if ($this->type === 'form') {
            $v =new ValidationList($this->messages);

        } elseif ($this->type === 'repeat') {
            $v =new ValidationRepeat($this->messages);

        } else {
            $v = new ValidationChain($this->messages);
            if ($this->type) {
                $filters = $this->typeFilter->getFilters($this->type);
            }
        }
        $filters = array_merge($filters, $this->filters);
        $v->addFilters($filters);
        if ($this->multiple) {
            $m = new ValidationRepeat($this->messages);;
            $m->add('0', $v);
            $v = $m;
        }
        return $v;
    }
}
