<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

class DefaultEmpty extends DefaultValue
{
    /**
     * DefaultValue constructor.
     */
    public function __construct()
    {
        parent::__construct('');
    }

    public function getFilterName(): string
    {
        return DefaultValue::class;
    }
}