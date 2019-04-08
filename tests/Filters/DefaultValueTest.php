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
    private function buildResult($value=null)
    {
        return new Result($value);
    }
    public function testDefaultValueOnNull()
    {
        $default = new DefaultValue(['default' => 'tested']);
        $result = $this->buildResult(null);
        $default($result);
        $this->assertEquals('tested', $result->value());
    }

    public function testDefaultValueOnEmpty()
    {
        $default = new DefaultValue(['default' => 'tested']);
        $result = $this->buildResult('');
        $default($result);
        $this->assertEquals('tested', $result->value());
    }

    public function testDefaultValueWhenValueIsSet()
    {
        $default = new DefaultValue(['default' => 'tested']);
        $result = $this->buildResult('test-me');
        $default($result);
        $this->assertEquals('test-me', $result->value());
    }

    public function testDefaultNull()
    {
        $default = new DefaultNull();
        $result = $this->buildResult('');
        $default($result);
        $this->assertTrue(null === $result->value());

        $default = new DefaultNull();
        $result = $this->buildResult(null);
        $default($result);
        $this->assertTrue(null === $result->value());
    }

    public function testDefaultEmpty()
    {
        $default = new DefaultEmpty();
        $result = $this->buildResult('');
        $default($result);
        $this->assertTrue('' === $result->value());

        $default = new DefaultEmpty();
        $result = $this->buildResult(null);
        $default($result);
        $this->assertTrue('' === $result->value());
    }

    public function testAllDefaultFiltersHaveSameName()
    {
        $empty = new DefaultEmpty();
        $null = new DefaultNull();
        $default = new DefaultValue(['default' => 'tested']);

        $this->assertEquals($default->getFilterName(), $empty->getFilterName());
        $this->assertEquals($default->getFilterName(), $null->getFilterName());
        $this->assertEquals(DefaultValue::class, $default->getFilterName());
    }
}
