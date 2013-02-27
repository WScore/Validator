<?php
namespace WScore\Validation;

return new Validate(
    new \WScore\Validation\Filter(),
    new \WScore\Validation\Message(),
    new \WScore\Validation\Rules()
);