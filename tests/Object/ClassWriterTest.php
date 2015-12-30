<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Tests\Object;

use Lucid\Writer\Object\Method;
use Lucid\Writer\Object\Argument;
use Lucid\Writer\Object\Property;
use Lucid\Writer\Object\ClassWriter;
use Lucid\Writer\Object\InterfaceMethod;

/**
 * @class ClassWriterTest
 * @see AbstractWriterTest
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ClassWriterTest extends AbstractWriterTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Lucid\Writer\Object\ClassWriter', $this->newObw());
    }

    /** @test */
    public function itShouldGenerateClasses()
    {
        $cg = $this->newObw('Foo', 'Acme');

        $cg->noAutoGenerateTag();
        $cg->setParent('Acme\Bar');
        $cg->addInterface('Acme\Baz');


        $this->assertEquals($this->getContents('class.0.php'), $cg->generate());
    }

    /** @test */
    public function itShouldBeAliasAware()
    {
        $cg = $this->newObw('Foo', 'Acme');
        $cg->noAutoGenerateTag();
        $cg->setParent('\Acme\Lib\Bar');
        $cg->addInterface('\Acme\Interfaces\Bar as FooBar');

        $this->assertEquals($this->getContents('class.1.php'), $cg->generate());
    }

    /** @test */
    public function itShouldHaveProperties()
    {
        $cg = $this->newObw('Foo', 'Acme');
        $cg->noAutoGenerateTag();

        $cg->setProperties([
            new Property('bar', Property::IS_PRIVATE),
            $p = new Property('baz', Property::IS_PUBLIC, 'string')
        ]);

        $p->setValue("'baz'");

        $this->assertEquals($this->getContents('class.3.php'), $cg->generate());
    }

    /** @test */
    public function itShouldAddTraits()
    {
        $cg = $this->newObw('Foo', 'Acme');
        $cg->noAutoGenerateTag();
        $cg->setParent('\Acme\Bar');
        $cg->addInterface('\Acme\Baz');
        $cg->addTrait('\Acme\Traits\FooTrait');
        $cg->addTrait('\Acme\Traits\BarTrait');

        $this->assertEquals($this->getContents('class.2.php'), $cg->generate());
    }

    /** @test */
    public function itShouldAddTraitReplacements()
    {
        $cg = $this->newObw('Foo', 'Acme');
        $cg->noAutoGenerateTag();
        $cg->setParent('\Acme\Bar');
        $cg->addInterface('\Acme\Baz');
        $cg->addTrait('\Acme\Traits\FooTrait');
        $cg->addTrait('\Acme\Traits\BarTrait');

        $cg->useTraitMethodAs('\Acme\Traits\FooTrait', 'bar', 'baz', 'private');
        $cg->replaceTraitConflict('\Acme\Traits\BarTrait', '\Acme\Traits\FooTrait', 'foo');

        $this->assertEquals($c = $this->getContents('class.2.1.php'), $cg->generate());
    }

    /** @test */
    public function itShouldHaveMethods()
    {
        $cg = $this->newObw('Foo', 'Acme');
        $cg->noAutoGenerateTag();

        $cg->addMethod($m = new Method('__construct'));
        $m->addArgument(new Argument('bar', 'Bar'));

        $this->assertEquals($c = $this->getContents('class.4.php'), $cg->generate());
    }

    /** @test */
    public function itShouldBeAbstract()
    {
        $cg = $this->newObw('Foo', 'Acme');
        $cg->noAutoGenerateTag();
        $cg->setAbstract(true);

        $this->assertEquals($c = $this->getContents('class.5.php'), $cg->generate());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowIfAddingInterfaceMethod()
    {
        $cg = $this->newObw('Foo', 'Acme');
        $cg->noAutoGenerateTag();

        $cg->addMethod($m = new InterfaceMethod('__construct'));
    }

    protected function newObw($name = 'MyClass', $namespace = null)
    {
        return new ClassWriter($name, $namespace);
    }
}
