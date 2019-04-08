<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

class DefaultNull extends DefaultValue
{
    /**
     * DefaultValue constructor.
     */
    public function __construct()
    {
        parent::__construct(['default' => null]);
    }

    public function getFilterName(): string
    {
        return DefaultValue::class;
    }
}