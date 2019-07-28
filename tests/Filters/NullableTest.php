<?php

namespace tests\Filters;

use PHPUnit\Framework\TestCase;
use WScore\Validator\Filters\Nullable;
use WScore\Validator\Filters\StringLength;
use WScore\Validator\Interfaces\ValidationInterface;
use WScore\Validator\ValidatorBuilder;

class NullableTest extends TestCase
{
    protected function buildValidation(): ValidationInterface
    {
        $b = new ValidatorBuilder();
        return $b->text();
    }

    public function testNullable()
    {
        $text = $this->buildValidation();
        $text->addFilters([
            StringLength::class => [StringLength::MIN => 3]
        ]);
        $result = $text->verify('');
        $this->assertFalse($result->isValid());

        $text = $this->buildValidation();
        $text->addFilters([
            Nullable::class,
            StringLength::class => [StringLength::MIN => 3]
        ]);
        $result = $text->verify('');
        $this->assertTrue($result->isValid());
    }
}
