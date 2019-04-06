<?php
/**
 * Created by PhpStorm.
 * User: asao
 * Date: 2019-03-27
 * Time: 13:41
 */

namespace tests;

use tests\Validation\Filters\AddPostfix;
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

    public function testBuildRepeatForm()
    {
        $vb = new ValidatorBuilder();
        $author = $vb->form()
            ->add('name', $vb->text([
                'filters' => [
                    AddPostfix::class => '-name',
                ]])
            )->add('corp', $vb->text([
                'filters' => [
                    AddPostfix::class => '-corp',
                ]])
            );

        $form = $vb->form(['name' => 'journal',])
            ->add('title',
                $vb->text([
                    'filters' => [
                        AddPostfix::class => '-title',
                    ]])
            )->add('authors',
                $vb->repeat()->add('author', $author)
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
        $this->assertEquals($input['title'].'-title', $output['title']);
        foreach ($results->getChild('authors') as $idx => $result) {
            $index = $idx + 1;
            $this->assertEquals("name{$index}-name", $result->getChild('name')->value());
            $this->assertEquals("corp{$index}-corp", $result->getChild('corp')->value());
        }

    }
}
