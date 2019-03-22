<?php
declare(strict_types=1);

namespace WScore\Validation\Types;

use WScore\Validation\Filters\FilterValidUtf8;
use WScore\Validation\Filters\MbConvertType;
use WScore\Validation\ValidationChain;

class TextType
{
    /**
     * @return ValidationChain
     */
    public function get()
    {
        $validator = new ValidationChain();
        $validator->setInitialMessage('not a valid text');
        $validator->setInputFilter(
            new FilterValidUtf8(),
            new MbConvertType()
        );

        return $validator;
    }
}