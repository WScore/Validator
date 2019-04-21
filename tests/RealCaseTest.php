<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use WScore\Validation\Filters\ConfirmWith;
use WScore\Validation\Filters\FilterArrayToValue;
use WScore\Validation\Filters\InArray;
use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\StringCases;
use WScore\Validation\Filters\StringLength;
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
                ['title' => 'first title', 'size' => 1234],
                ['title' => 'more tests here', 'publishedAt' => '2019-04-01', 'size' => 2345],
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
        $form = $vb->form()
            ->add('name', $vb->text([
                Required::class,
                StringCases::class => [StringCases::TO_LOWER, StringCases::UC_WORDS],
            ]))->add('email', $vb->email([
                Required::class,
                StringCases::class => [StringCases::TO_LOWER],
                ConfirmWith::class => [ConfirmWith::FIELD => 'email_check'],
            ]))->add('birthday', $vb->date([
                FilterArrayToValue::class => [
                    FilterArrayToValue::FIELDS => ['y', 'm', 'd'],
                    FilterArrayToValue::FORMAT => '%04d-%02d-%02d'
                ],
            ]));

        $address = $vb->form()
            ->add('zip', $vb([
                'type' => 'digits',
                Required::class,
                StringLength::class => [StringLength::LENGTH => 5],
            ]))
            ->add('address', $vb([
                'type' => 'text',
                Required::class,
            ]))
            ->add('region', $vb([
                'type' => 'text',
                Required::class,
                InArray::class => [
                    InArray::REPLACE => [
                        'abc' => 'ABC Country',
                        'def' => 'DEF Region',
                    ],
                ],
            ]));

        $posts = $vb->form()
            ->add('title', $vb->text([
                Required::class,
            ]))
            ->add('publishedAt', $vb->date())
            ->add('size', $vb->integer([
                Required::class,
            ]));

        $form->addRepeatedForm('posts', $posts);

        $form->add('address', $address);

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

        /* check address input. */
        $this->assertTrue($results->hasChild('address'));
        $address = $results->getChild('address');
        // check address.
        $this->assertEquals('12345', $address->getChild('zip')->value());
        $this->assertIsString($address->getChild('zip')->value());
        $this->assertEquals('city, street 101', $address->getChild('address')->value());
        $this->assertEquals('ABC Country', $address->getChild('region')->value());

        /* check posts input. */
        $this->assertTrue($results->hasChild('posts'));
        $posts = $results->getChild('posts');
        // check post0.
        $post0 = $posts->getChild(0);
        $this->assertEquals('first title', $post0->getChild('title')->value());
        $this->assertEquals(null, $post0->getChild('publishedAt')->value());
        $this->assertEquals(1234, $post0->getChild('size')->value());
        $this->assertIsInt($post0->getChild('size')->value());
        // check post1.
        $post1 = $posts->getChild(1);
        $this->assertEquals('more tests here', $post1->getChild('title')->value());
        $this->assertEquals('20190401', $post1->getChild('publishedAt')->value()->format('Ymd'));
        $this->assertEquals(2345, $post1->getChild('size')->value());
        $this->assertIsInt($post1->getChild('size')->value());
    }
}