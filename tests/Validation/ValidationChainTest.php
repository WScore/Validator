<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use tests\Validation\Filters\AddPostfix;
use WScore\Validation\Filters\Required;
use WScore\Validation\Locale\Messages;
use WScore\Validation\ValidatorBuilder;
use WScore\Validation\Validators\Result;
use WScore\Validation\Validators\ValidationChain;

class ValidationChainTest extends TestCase
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

    public function testConstruction()
    {
        $chain = $this->buildValidationChain();
        $this->assertEquals(ValidationChain::class,get_class($chain));
    }

    public function testVerify()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters([new AddPostfix('-verified')]);
        $result = $chain->verify('test-verify');

        $this->assertEquals(Result::class, get_class($result));
        $this->assertEquals('test-verify-verified', $result->value());
    }

    public function testHasGetAllAndRemove()
    {
        $vb = new ValidatorBuilder();
        $form = $vb->form();
        $this->assertFalse($form->has('name'));

        $form->add( 'name',
                $vb->text()
            )
            ->add( 'corp',
                $vb->text()
            )
            ;
        $this->assertTrue($form->has('name'));
        $this->assertEquals('name', $form->get('name')->getName());

        $form->remove('name');
        $this->assertFalse($form->has('name'));
        $this->assertTrue($form->has('corp'));
    }

    public function testErrorMessageInValidation()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters([new Required()]);
        $chain->setErrorMessage('tested error message');
        $result = $chain->verify('');

        $this->assertFalse($result->isValid());
        $this->assertEquals(['required', 'tested error message'], $result->getErrorMessage());
    }

    public function testRemoveFiltersWhenSetToFalse()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters([new Required()]);
        $result = $chain->verify('');
        $this->assertFalse($result->isValid());

        $chain->addFilters([Required::class => false]);
        $result = $chain->verify('');
        $this->assertTrue($result->isValid());
    }

    public function testFilterArguments()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters([AddPostfix::class => true]);
        $result = $chain->verify('test');
        $this->assertEquals('test-tested', $result->value());

        $chain->addFilters([AddPostfix::class => '-more']);
        $result = $chain->verify('test');
        $this->assertEquals('test-more', $result->value());
    }
}
