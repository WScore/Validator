<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

class DefaultNull extends DefaultValue
{
    /**
     * DefaultValue constructor.
     */
    public function __construct()
    {
        parent::__construct(null);
    }

    public function getFilterName(): string
    {
        return DefaultValue::class;
    }
}