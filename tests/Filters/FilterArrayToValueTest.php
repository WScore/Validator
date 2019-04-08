<?php

namespace tests\Filters;

use WScore\Validation\Filters\FilterArrayToValue;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Filters\FilterValidUtf8;
use WScore\Validation\Locale\Messages;
use WScore\Validation\ValidatorBuilder;

class FilterArrayToValueTest extends TestCase
{
    /**
     * @var ValidatorBuilder
     */
    private $vb;

    public function setUp(): void
    {
        $this->vb = new ValidatorBuilder();
    }

    public function testFormatArrayInput()
    {
        $chain = $this->vb->chain();
        $chain->addFilters([
            new FilterArrayToValue([
                'fields' => ['Y', 'M'],
                'format' => '%04d.%02d',
            ]),
            new FilterValidUtf8(),
        ]);
        $result = $chain->verify([
            'Y' => 2019,
            'M' => 4,
        ]);
        $this->assertEquals('2019.04', $result->value());
    }

    public function testImplodeArrayInput()
    {
        $chain = $this->vb->chain();
        $chain->addFilters([
            new FilterArrayToValue([
                'fields' => ['Y', 'M'],
            ]),
        ]);
        $result = $chain->verify([
            'Y' => 2019,
            'M' => 4,
        ]);
        $this->assertEquals('2019-4', $result->value());
    }

    public function testInputNotArray()
    {
        $chain = $this->vb->chain();
        $chain->addFilters([
            new FilterArrayToValue([
                'fields' => ['Y', 'M'],
            ]),
        ]);
        $result = $chain->verify('2019/04');
        $this->assertEquals('2019/04', $result->value());
    }

    public function testMissingOneField()
    {
        $chain = $this->vb->chain();
        $chain->addFilters([
            new FilterArrayToValue([
                'fields' => ['Y', 'M', 'D'],
            ]),
        ]);
        $result = $chain->verify([
            'Y' => 2019,
            'D' => 30,
        ]);
        $this->assertFalse($result->isValid());
        $this->assertEquals('', $result->value());

        $result->finalize(Messages::create('en'));
        $this->assertEquals(['The input is missing "M" field.'], $result->getErrorMessage());
    }
}