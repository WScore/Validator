<?php
/**
 * Created by PhpStorm.
 * User: wsjp
 * Date: 2019/03/26
 * Time: 6:22
 */

namespace tests\Validation;

use tests\Validation\Filters\AddPostfix;
use WScore\Validation\Locale\Messages;
use WScore\Validation\ResultList;
use WScore\Validation\ValidationMultiple;
use PHPUnit\Framework\TestCase;

class ValidationMultipleTest extends TestCase
{
    /**
     * @param string $locale
     * @return ValidationMultiple
     */
    public function buildValidationMultiple($locale = 'en')
    {
        $messages = Messages::create($locale);
        $chain = new ValidationMultiple($messages);

        return $chain;
    }

    public function testInitialize()
    {
        $list = $this->buildValidationMultiple();
        $input = ['test' => 'test1', 'more' => 'test2'];
        $result = $list->initialize($input);
        $this->assertEquals(ResultList::class, get_class($result));
        $this->assertEquals($input, $result->value());
        $this->assertTrue($result->hasChildren());
        $this->assertEquals('test1', $result->getChild('test')->value());
        $this->assertEquals('test2', $result->getChild('more')->value());
    }

    public function testValidate()
    {
        $list = $this->buildValidationMultiple();
        $list->addFilters(
            new AddPostfix('-multi')
        );
        $input = ['test' => 'test1', 'more' => 'test2'];
        $result = $list->initialize($input);
        $result = $list->validate($result);
        $result->finalize();
        $this->assertEquals('test1-multi', $result->getChild('test')->value());
        $this->assertEquals('test2-multi', $result->getChild('more')->value());
    }

    public function testVerify()
    {
        $list = $this->buildValidationMultiple();
        $list->addFilters(
            new AddPostfix('-multi')
        );
        $input = ['test' => 'test1', 'more' => 'test2'];
        $result = $list->verify($input);
        $this->assertEquals('test1-multi', $result->getChild('test')->value());
        $this->assertEquals('test2-multi', $result->getChild('more')->value());
    }

}
