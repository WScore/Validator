<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use WScore\Validation\Filters\ConfirmWith;
use WScore\Validation\Filters\FilterArrayToValue;
use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\StringCases;
use WScore\Validation\Interfaces\ValidationInterface;
use WScore\Validation\ValidatorBuilder;
use WScore\Validation\Validators\ValidationList;

class RealCaseTest extends TestCase
{
    private function getInput(): array
    {
        $input = [
            'name' => 'MY NAME',
            'email' => 'Email@Example.Com',
            'email_check' => 'Email@Example.Com',
            'birthday' => [
                'y' => 1999,
                'm' => 12,
                'd' => 31,
            ],
            'address' => [
                'zip' => '12345',
                'address' => 'city, street 101',
                'region' => 'abc',
            ],
            'posts' => [
                ['title' => 'first title', 'date' => '2018-04-01', 'size' => 1234],
                ['title' => 'more tests here', 'date' => '2019-04-01', 'size' => 2345],
            ],
        ];
        return $input;
    }

    /**
     * @return ValidationInterface|ValidationList
     */
    private function getValidator(): ValidationInterface
    {
        $vb = new ValidatorBuilder();
        $form = $vb->form();

        $form->add('name', $vb->text([
            Required::class,
            StringCases::class => [StringCases::TO_LOWER, StringCases::UC_WORDS],
        ]))->add('email', $vb->email([
            Required::class,
            StringCases::class => [StringCases::TO_LOWER],
            ConfirmWith::class => [ConfirmWith::FIELD => 'email_check'],
        ]))->add('birthday', $vb->date([
            FilterArrayToValue::class => ['fields' => ['y', 'm', 'd'], 'format' => '%04d-%02d-%02d'],
        ]));

        return $form;
    }

    public function testClear()
    {
        $input = $this->getInput();
        $form = $this->getValidator();
        $results = $form->verify($input);

        $this->assertTrue($results->isValid());
        // check name.
        $this->assertTrue($results->hasChild('name'));
        $this->assertEquals('My Name', $results->getChild('name')->value());
        // check email.
        $this->assertTrue($results->hasChild('email'));
        $this->assertEquals('email@example.com', $results->getChild('email')->value());
        // check birthday.
        $this->assertTrue($results->hasChild('birthday'));
        $birthday = $results->getChild('birthday')->value();
        $this->assertEquals('DateTimeImmutable', get_class($birthday));
        $this->assertEquals('19991231', $birthday->format('Ymd'));
    }
}