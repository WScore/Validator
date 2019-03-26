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

class ValidatorBuilder
{
    const TYPE_CHAIN  = 'Chain';
    const TYPE_MULTI  = 'Multiple';
    const TYPE_LIST   = 'List';
    const TYPE_REPEAT = 'Repeat';

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
    private $type = self::TYPE_CHAIN;

    /**
     * ValidatorBuilder constructor.
     * @param string $locale
     */
    public function __construct($locale = 'en')
    {
        $this->messages = Messages::create($locale);
        $this->typeFilter = TypeFilters::create($locale);
    }

    public function multiple(): self
    {
        $self = clone($this);
        $self->type = self::TYPE_MULTI;
        return $self;
    }

    public function form(string $name = null): ValidationList
    {
        return new ValidationList($this->messages, $name);
    }

    public function repeat(string $name = null): ValidationRepeat
    {
        return new ValidationRepeat($this->messages, $name);
    }

    public function text(string $name = null): ValidationInterface
    {
        $type = 'text';
        return $this->buildByType($name, $type);
    }

    private function buildByType(?string $name, string $type): ValidationInterface
    {
        if ($this->type === self::TYPE_MULTI) {
            $v = new ValidationMultiple($this->messages, $name);
        } else {
            $v = new ValidationChain($this->messages, $name);
        }
        $filters = $this->typeFilter->getFilters($type);
        $v->addFilters(...$filters);
        return $v;
    }
}