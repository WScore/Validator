<?php

namespace tests\Filters;

use WScore\Validator\Filters\ConfirmWith;
use PHPUnit\Framework\TestCase;
use WScore\Validator\Interfaces\ValidationInterface;
use WScore\Validator\ValidatorBuilder;
use WScore\Validator\Validators\ValidationList;

class ConfirmWithTest extends TestCase
{
    /**
     * @return ValidationInterface|ValidationList
     */
    public function buildForm()
    {
        $vb = new ValidatorBuilder();
        $form = $vb->form()
            ->add('email', $vb->email([ConfirmWith::class]));
        return $form;
    }

    public function testMissingConfirmation()
    {
        $form = $this->buildForm();
        $result = $form->verify([
            'email' => 'test@example.com',
        ]);
        $this->assertFalse($result->isValid());
        $this->assertFalse($result->getChild('email')->isValid());
        $this->assertEquals(
            ['The field for confirmation is empty.'],
            $result->getChild('email')->getErrorMessage()
        );
    }

    public function testDiffersConfirmation()
    {
        $form = $this->buildForm();
        $result = $form->verify([
            'email' => 'test@example.com',
            'email_confirmation' => 'test0@example.com',
        ]);
        $this->assertFalse($result->isValid());
        $this->assertFalse($result->getChild('email')->isValid());
        $this->assertEquals(
            ['The input differs from confirmation.'],
            $result->getChild('email')->getErrorMessage()
        );
    }

    public function testConfirmationMatches()
    {
        $form = $this->buildForm();

        $result = $form->verify([
            'email' => 'test@example.com',
            'email_confirmation' => 'test@example.com',
        ]);
        $this->assertTrue($result->isValid());
        $this->assertTrue($result->getChild('email')->isValid());
    }
}
