<?php
declare(strict_types=1);

namespace WScore\Validation\Types;

use WScore\Validation\Filters\FilterValidUtf8;
use WScore\Validation\Filters\MbConvertType;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Locale\TypeFilters;
use WScore\Validation\ValidationChain;

class ValidationType
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
     * @return ValidationChain
     */
    public function text()
    {
        $filters = $this->typeFilter->getFilters('text');
        $validation = new ValidationChain();
        $validation->addFilters(...$filters);

        return $validation;
    }
}