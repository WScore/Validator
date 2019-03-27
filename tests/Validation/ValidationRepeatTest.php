<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: wsjp
 * Date: 2019/03/27
 * Time: 7:15
 */

namespace tests\Validation;

use PHPUnit\Framework\TestCase;
use tests\Validation\Filters\AddPostfix;
use WScore\Validation\ValidatorBuilder;
use WScore\Validation\Validators\ValidationRepeat;

class ValidationRepeatTest extends TestCase
{
    /**
     * @var ValidatorBuilder
     */
    private $ValidationBuilder;

    protected function setUp(): void
    {
        $this->ValidationBuilder = new ValidatorBuilder('en');
    }

    public function testConstruction()
    {
        $vb = $this->ValidationBuilder;
        $repeat = $vb->repeat(['name' => 'test']);
        $text = $vb->text()->addFilters(new AddPostfix('-repeat'));
        $repeat->addChild('author', $text);
        $this->assertEquals(ValidationRepeat::class, get_class($repeat));
    }

    public function testVerify()
    {
        $vb = $this->ValidationBuilder;
        $repeat = $vb->repeat(['name' => 'test'])
            ->addChild('author', $vb->text()->addFilters(new AddPostfix('-repeat')));
        $input = [
            'name1',
            'name2'
        ];
        $result = $repeat->verify($input);
        $this->assertTrue($result->isValid());
        $this->assertEquals('name1', $result->getChild(0)->getOriginalValue());
        $this->assertEquals('name1-repeat', $result->getChild(0)->value());
        foreach ($result as $i => $item) {
            $index = $i + 1;
            $this->assertEquals("name{$index}", $result->getChild($i)->getOriginalValue());
            $this->assertEquals("name{$index}-repeat", $result->getChild($i)->value());
        }
    }

    public function testOneToManyForm()
    {
        $vb = $this->ValidationBuilder;
        $formAuthor = $vb->form()
            ->addChild(
                'name', $vb->text()->addFilters(new AddPostfix('-name'))
            )->addChild(
                'corp', $vb->text()->addFilters(new AddPostfix('-corp'))
            );
        $form = $vb->form()
            ->addChild(
                'title',
                $vb->text()->addFilters(new AddPostfix('-form'))
            )->addChild(
                'authors',
                $vb->repeat()->addChild('author', $formAuthor)
            );
        $input = [
            'title' => 'new validation',
            'authors' => [
                ['name' => 'name1', 'corp' => 'corp1'],
                ['name' => 'name2', 'corp' => 'corp2'],
            ],
        ];
        $results = $form->verify($input);
        $output = $results->value();
        $this->assertTrue($results->isValid());
        $this->assertEquals($input['title'].'-form', $output['title']);
        foreach ($results->getChild('authors') as $idx => $result) {
            $index = $idx + 1;
            $this->assertEquals("name{$index}-name", $result->getChild('name')->value());
            $this->assertEquals("corp{$index}-corp", $result->getChild('corp')->value());
        }
    }
}
