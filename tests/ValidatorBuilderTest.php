<?php

namespace tests;

use tests\Validation\Filters\AddPostfix;
use WScore\Validation\Filters\Required;
use WScore\Validation\Filters\StringLength;
use WScore\Validation\ValidatorBuilder;
use PHPUnit\Framework\TestCase;

class ValidatorBuilderTest extends TestCase
{
    public function testBuild()
    {
        $vb = new ValidatorBuilder();
        $text = $vb->text([
            'name' => 'tests',
            'multiple' => false,
            'filters' => [
                AddPostfix::class => '-builder',
            ],
        ]);
        $result = $text->verify('good');
        $this->assertTrue($result->isValid());
        $this->assertEquals('good-builder', $result->value());
    }

    public function testMultipleOption()
    {
        $vb = new ValidatorBuilder();
        $text = $vb->text([
            'name' => 'tests',
            'multiple' => true,
            'filters' => [
                AddPostfix::class => '-builder',
            ],
        ]);
        $result = $text->verify(['pretty', 'good']);
        $this->assertTrue($result->isValid());
        $this->assertEquals(['pretty-builder', 'good-builder'], $result->value());
    }

    public function testNestedForm()
    {
        $vb = new ValidatorBuilder();
        $form = $vb->form()
            ->add('name', $vb->text([Required::class]))
            ->add('age', $vb->integer())
            ->add('address',
                $vb->form()
                    ->add('address', $vb->text())
                    ->add('countryCode', $vb->text([StringLength::class=>['length'=>3]]))
            );
        $input = [
            'name' => 'test-me',
            'age' => 25,
            'address' => [
                'address' => 'Some City, PHP Street',
                'countryCode' => 'ABC',
            ]
        ];
        $result = $form->verify($input);
        $this->assertTrue($result->isValid());
        $this->assertEquals($input, $result->value());
    }

    public function testBuildRepeatForm()
    {
        $vb = new ValidatorBuilder();

        $form = $vb->form()
            ->add('title', $vb->text([Required::class]))
            ->addRepeatedForm('authors',
                $vb->form()
                    ->add('name', $vb->text([AddPostfix::class => '-name']))
                    ->add('age', $vb->integer())
            );

        $input = [
            'title' => 'New Validation',
            'authors' => [
                ['name' => 'name1', 'age' => '26'],
                ['name' => 'name2', 'age' => '27'],
            ],
        ];
        $results = $form->verify($input);
        $output = $results->value();
        $this->assertTrue($results->isValid());
        $this->assertEquals($input['title'], $output['title']);
        foreach ($results->getChild('authors') as $idx => $result) {
            $index = $idx + 1;
            $this->assertEquals("name{$index}-name", $result->getChild('name')->value());
            $this->assertEquals(26+$idx, $result->getChild('age')->value());
        }

    }
}
