<?php
/**
 * Created by PhpStorm.
 * User: wsjp
 * Date: 2019/03/27
 * Time: 20:21
 */

namespace tests\Filters;

use WScore\Validation\Filters\DefaultEmpty;
use WScore\Validation\Filters\DefaultNull;
use WScore\Validation\Filters\DefaultValue;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Validators\Result;

class DefaultValueTest extends TestCase
{
    public function testDefaultValueOnNull()
    {
        $default = new DefaultValue('tested');
        $result = new Result(null, null);
        $default($result);
        $this->assertEquals('tested', $result->value());
    }

    public function testDefaultValueOnEmpty()
    {
        $default = new DefaultValue('tested');
        $result = new Result(null, '');
        $default($result);
        $this->assertEquals('tested', $result->value());
    }

    public function testDefaultValueWhenValueIsSet()
    {
        $default = new DefaultValue('tested');
        $result = new Result(null, 'test-me');
        $default($result);
        $this->assertEquals('test-me', $result->value());
    }

    public function testDefaultNull()
    {
        $default = new DefaultNull();
        $result = new Result(null, '');
        $default($result);
        $this->assertTrue(null === $result->value());

        $default = new DefaultNull();
        $result = new Result(null, null);
        $default($result);
        $this->assertTrue(null === $result->value());
    }

    public function testDefaultEmpty()
    {
        $default = new DefaultEmpty();
        $result = new Result(null, '');
        $default($result);
        $this->assertTrue('' === $result->value());

        $default = new DefaultEmpty();
        $result = new Result(null, null);
        $default($result);
        $this->assertTrue('' === $result->value());
    }

    public function testAllDefaultFiltersHaveSameName()
    {
        $empty = new DefaultEmpty();
        $null = new DefaultNull();
        $default = new DefaultValue('tested');

        $this->assertEquals($default->getFilterName(), $empty->getFilterName());
        $this->assertEquals($default->getFilterName(), $null->getFilterName());
        $this->assertEquals(DefaultValue::class, $default->getFilterName());
    }
}
