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
use WScore\Validation\ValidatorBuilder;
use WScore\Validation\Validators\ResultList;
use WScore\Validation\Validators\ValidationChain;
use WScore\Validation\Validators\ValidationMultiple;
use PHPUnit\Framework\TestCase;

class ValidationMultipleTest extends TestCase
{
    /**
     * @param string $locale
     * @return ValidationChain
     */
    public function buildValidationMultiple($locale = 'en')
    {
        $messages = Messages::create($locale);
        $chain = new ValidationChain($messages);
        $chain->setMultiple();

        return $chain;
    }

    public function testVerify()
    {
        $list = $this->buildValidationMultiple();
        $list->addFilters(
            new AddPostfix('-multi')
        );
        $input = ['test' => 'test1', 'more' => 'test2'];
        $result = $list->verify($input);

        $this->assertEquals($input, $result->getOriginalValue());
        $this->assertTrue($result->hasChildren());
        $this->assertEquals('test1-multi', $result->getChild('test')->value());
        $this->assertEquals('test2-multi', $result->getChild('more')->value());
    }

}
