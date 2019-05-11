<?php

namespace tests\Filters;

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
        $default->apply($result);
        $this->assertEquals('tested', $result->value());
    }

    public function testDefaultValueOnEmpty()
    {
        $default = new DefaultValue(['default' => 'tested']);
        $result = $this->buildResult('');
        $default->apply($result);
        $this->assertEquals('tested', $result->value());
    }

    public function testDefaultValueWhenValueIsSet()
    {
        $default = new DefaultValue(['default' => 'tested']);
        $result = $this->buildResult('test-me');
        $default->apply($result);
        $this->assertEquals('test-me', $result->value());
    }

    public function testDefaultNull()
    {
        $default = new DefaultValue(['default' => null]);
        $result = $this->buildResult('');
        $default->apply($result);
        $this->assertTrue(null === $result->value());

        $default = new DefaultValue(['default' => null]);
        $result = $this->buildResult(null);
        $default->apply($result);
        $this->assertTrue(null === $result->value());
    }

    public function testDefaultEmpty()
    {
        $default = new DefaultValue(['default' => '']);
        $result = $this->buildResult('');
        $default->apply($result);
        $this->assertTrue('' === $result->value());

        $default = new DefaultValue(['default' => '']);
        $result = $this->buildResult(null);
        $default->apply($result);
        $this->assertTrue('' === $result->value());
    }
}
