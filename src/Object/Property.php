<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

use Lucid\Writer\Writer;
use Lucid\Writer\GeneratorInterface;

/**
 * @class Property
 * @see MemberInterface
 * @see GeneratorInterface
 * @see Annotateable
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Property extends Annotateable implements MemberInterface, GeneratorInterface
{
    use VisibilityHelperTrait;

    /** @var string */
    private $name;

    /** @var string */
    private $visibility;

    /** @var string */
    private $prefix;

    /** @var string */
    private $type;

    /** @var string */
    private $value;

    /** @var bool */
    private $inline = false;

    /**
     * Constructor.
     *
     * @param string  $name
     * @param string  $visibility
     * @param string  $type
     * @param boolean $static
     */
    public function __construct($name, $visibility = self::IS_PUBLIC, $type = 'mixed', $static = false)
    {
        $this->setVisibility($visibility);
        $this->setStatic($static);

        $this->name = $name;
        $this->type = $type;

        parent::__construct();
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }

    /**
     * Set the property value.
     *
     * @param string $value
     *
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Set the properties visibility.
     *
     * @param string $visibility
     *
     * @return void
     */
    public function setVisibility($visibility)
    {
        $this->checkVisibility($visibility);
        $this->visibility = $visibility;
    }

    /**
     * Set the properties type.
     *
     * @param string $type
     *
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Set this property to be static.
     *
     * @param boolean $static
     *
     * @return void
     */
    public function setStatic($static)
    {
        $this->prefix = (bool)$static ? ' static' : '';
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $this->getDoc($writer = new Writer)
            ->writeln(sprintf('%s%s $%s', $this->visibility, $this->prefix, $this->name));

        if (null !== $this->value) {
            $writer->appendln(' = ' . $this->value);
        }

        $writer
            ->appendln(';')
            ->setOutputIndentation(1);

        return $raw ? $writer : $writer->dump();
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareAnnotations(DocBlock $block)
    {
        if (!$block->hasDescription()) {
            $block->setInline(true);
        }

        if ($block->hasAnnotations()) {
            $block->unshiftAnnotation(null);
        }

        $block->unshiftAnnotation('var', $this->type);
    }
}
