<?php

namespace tests\Filters;

use DateTimeImmutable;
use WScore\Validation\Filters\ConvertDateTime;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Validators\Result;

class FilterDateTimeTest extends TestCase
{

    public function test__invoke()
    {
        $filter = new ConvertDateTime();
        $input = new Result('2019-04-01');
        $return = $filter->__invoke($input);
        $this->assertNull($return);
        $this->assertTrue($input->isValid());
        $this->assertEquals(DateTimeImmutable::class, get_class($input->value()));
        $this->assertEquals('2019/04/01', $input->value()->format('Y/m/d'));
    }

    /**
     * @dataProvider dateValueProvider
     * @param $value
     */
    public function testInputs($value)
    {
        $filter = new ConvertDateTime();
        $input = new Result($value);
        $return = $filter($input);
        $this->assertNull($return);
        $this->assertTrue($input->isValid());
        $this->assertEquals(DateTimeImmutable::class, get_class($input->value()));
        $this->assertEquals('2019/04/01', $input->value()->format('Y/m/d'));
    }

    public function dateValueProvider()
    {
        return [
            ['2019-04'],
            ['2019-04-01'],
            ['2019/04/01'],
            ['20190401'],
            ['2019-04-01 01:00:00'],
            ['2019-04-01T01:00:00'],
        ];
    }

    public function testCreateDateTimeUsingFormat()
    {
        $filter = new ConvertDateTime(['format' =>'Y.m.d']);
        $input = new Result('2019.04.01');
        $return = $filter->__invoke($input);
        $this->assertNull($return);
        $this->assertTrue($input->isValid());
        $this->assertEquals(DateTimeImmutable::class, get_class($input->value()));
        $this->assertEquals('2019/04/01', $input->value()->format('Y/m/d'));
    }

    public function testInvalidUtf8ValueReturnsError()
    {
        $filter = new ConvertDateTime();
        $input = new Result(mb_convert_encoding('日本語','SJIS', 'UTF-8'));
        $return = $filter->__invoke($input);
        $this->assertFalse($return->isValid());
        $this->assertNull($input->value());

        $return->finalize(Messages::create());
        $this->assertEquals(['Invalid DateTime input value.'], $input->getErrorMessage());

        $return->finalize(Messages::create('ja'));
        $this->assertEquals(['日付と認識できません。'], $input->getErrorMessage());
    }
}
