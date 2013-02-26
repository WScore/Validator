<?php
namespace WScore\Validation;

return new \WScore\Validation\Validation(
    include( __DIR__ . '/validate.php'),
    new \WScore\Validation\Rules()
);