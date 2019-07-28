<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use tests\Validation\Filters\AddPostfix;
use WScore\Validator\Filters\Required;
use WScore\Validator\Locale\Messages;
use WScore\Validator\ValidatorBuilder;
use WScore\Validator\Validators\Result;
use WScore\Validator\Validators\ValidationChain;

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
        $this->assertEquals(['The input field is required.', 'tested error message'], $result->getErrorMessage());
    }

    public function testHasGetAndRemoveFilter()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters([new AddPostfix('-filter')]);
        $result = $chain->verify('test');
        $this->assertEquals('test-filter', $result->value());

        $this->assertTrue($chain->getFilters()->has(AddPostfix::class));
        /** @var AddPostfix $filter */
        $filter = $chain->getFilters()->get(AddPostfix::class);
        $filter->setPrefix('-mod-filter');
        $result = $chain->verify('test');
        $this->assertEquals('test-mod-filter', $result->value());

        $chain->getFilters()->remove(AddPostfix::class);
        $result = $chain->verify('test');
        $this->assertEquals('test', $result->value());
    }

    public function testFilterArguments()
    {
        $chain = $this->buildValidationChain();
        $chain->addFilters([AddPostfix::class]);
        $result = $chain->verify('test');
        $this->assertEquals('test-tested', $result->value());

        $chain->addFilters([AddPostfix::class => '-more']);
        $result = $chain->verify('test');
        $this->assertEquals('test-more', $result->value());
    }
}
