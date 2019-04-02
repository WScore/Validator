<?php
declare(strict_types=1);

namespace tests\Validation;

use PHPUnit\Framework\TestCase;
use tests\Validation\Filters\AddPostfix;
use WScore\Validation\Filters\Required;
use WScore\Validation\Locale\Messages;
use WScore\Validation\Validators\ValidationChain;
use WScore\Validation\Validators\ValidationList;

class ValidationListTest extends TestCase
{
    /**
     * @param string $locale
     * @return ValidationChain
     */
    public function buildValidationChain($locale = 'en')
    {
        $messages = Messages::create($locale);
        $chain = new ValidationChain($messages);

        return $chain;
    }

    /**
     * @param string $locale
     * @return ValidationList
     */
    public function buildValidationList($locale = 'en')
    {
        $messages = Messages::create($locale);
        $list = new ValidationList($messages);

        return $list;
    }

    /**
     * @return ValidationList
     */
    public function buildTestList()
    {
        $list = $this->buildValidationList();
        $list->add(
            'test',
            $this->buildValidationChain()
                ->addFilters([
                    new AddPostfix('-test')
                ])
        );
        $list->add(
            'more',
            $this->buildValidationChain()
                ->addFilters([
                    new AddPostfix('-more')
                ])
        );
        return $list;
    }

    public function testVerify()
    {
        $list = $this->buildTestList();
        $input = ['test' => 'test1', 'more' => 'test2'];
        $result = $list->verify($input);

        $this->assertEquals($input, $result->getOriginalValue());
        $this->assertEquals('test1-test', $result->getChild('test')->value());
        $this->assertEquals('test2-more', $result->getChild('more')->value());
        $this->assertTrue($result->isValid());
    }

    public function testValidationsErrorMessage()
    {
        $list = $this->buildTestList();
        $list->get('test')->addFilters([new Required()]);
        $list->get('more')->addFilters([new Required()]);
        $list->setErrorMessage('list failed');
        $input = ['test' => '', 'more' => 'test2'];
        $result = $list->verify($input);

        $this->assertFalse($result->isValid());
        $this->assertFalse($result->isValid());
        $this->assertEquals(['list failed'], $result->getErrorMessage());
        $this->assertEquals(['required'], $result->summarizeErrorMessages()['test']);
    }
}
