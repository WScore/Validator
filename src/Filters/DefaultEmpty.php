<?php
declare(strict_types=1);

namespace WScore\Validation\Filters;

use WScore\Validation\Interfaces\FilterInterface;
use WScore\Validation\Interfaces\ResultInterface;

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