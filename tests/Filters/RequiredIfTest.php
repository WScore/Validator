<?php
declare(strict_types=1);

namespace tests\Filters;

use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\RequiredIf;
use PHPUnit\Framework\TestCase;
use WScore\Validation\Validators\Result;
use WScore\Validation\Validators\ResultList;

class RequiredIfTest extends TestCase
{
    private function buildResult($value=null, $name=null)
    {
        return new Result($value, $name);
    }
    public function testRequiredIfWithoutSettingsReturnsFalseWhenValueIsEmpty()
    {
        $result = $this->buildResult('');
        $required = new RequiredIf();
        $required->apply($result);
        $this->assertFalse($result->isValid());
    }

    public function testRequiredIfWithoutSettingsReturnsTrueWhenValueIsSet()
    {
        $result = $this->buildResult('value');
        $required = new RequiredIf();
        $required->apply($result);
        $this->assertTrue($result->isValid());
        $this->assertEquals('value', $result->value());
    }

    public function testRequiredSetsFalseIfValueIsNotSet()
    {
        $required = new RequiredIf([RequiredIf::FIELD => 'more']);

        $resultList = $this->buildResultList();
        $result = $resultList->getChild('some');

        $required->apply($result);
        $this->assertTrue($result->isValid());

        $resultList = $this->buildResultList('more-value');
        $result = $resultList->getChild('some');

        $required->apply($result);
        $this->assertFalse($result->isValid());
    }

    public function testRequiredIf()
    {
        $resultList = $this->buildResultList();
        $result = $resultList->getChild('some');

        $required = new RequiredIf();
        $required->apply($result);
        $this->assertFalse($result->isValid());

        $resultList = $this->buildResultList();
        $result = $resultList->getChild('some');

        $required = new RequiredIf([RequiredIf::FIELD => 'more']);
        $required->apply($result);
        $this->assertTrue($result->isValid());
    }

    public function testRequiredIfValue()
    {
        $resultList = $this->buildResultList('not-more');
        $result = $resultList->getChild('some');

        $required = new RequiredIf([RequiredIf::FIELD => 'more', RequiredIf::VALUE => 'more-more']);
        $required->apply($result);
        $this->assertTrue($result->isValid());

        $resultList = $this->buildResultList('more-more');
        $result = $resultList->getChild('some');

        $required = new RequiredIf([RequiredIf::FIELD => 'more', RequiredIf::VALUE => 'more-more']);
        $required->apply($result);
        $this->assertFalse($result->isValid());
    }

    /**
     * @param string $more
     * @return ResultList
     */
    public function buildResultList($more = ''): ResultList
    {
        $input = ['some' => '', 'more' => $more];
        $resultList = new ResultList($input);
        foreach ($input as $key => $val) {
            $resultList->addResult($this->buildResult($val, $key));
        }
        return $resultList;
    }

    public function testNullable()
    {
        $inputAll = $this->buildResultList('have-value');
        $input = $inputAll->getChild('some');

        $if = new RequiredIf([
            RequiredIf::FIELD => 'more',
            RequiredIf::VALUE => 'not-required',
            RequiredIf::NULLABLE => false,
        ]);
        $result = $if->apply($input);
        $this->assertTrue(is_null($result));

        $if = new RequiredIf([
            RequiredIf::FIELD => 'more',
            RequiredIf::VALUE => 'not-required',
            RequiredIf::NULLABLE => true,
        ]);
        $result = $if->apply($input);
        $this->assertTrue(!is_null($result));
    }
}
