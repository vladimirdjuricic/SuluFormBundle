<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\FormBundle\ListBuilder;

use Sulu\Bundle\FormBundle\Entity\Dynamic;
use Sulu\Bundle\FormBundle\Entity\Form;
use Sulu\Component\Rest\ListBuilder\FieldDescriptor;
use Sulu\Component\Rest\ListBuilder\FieldDescriptorInterface;

/**
 * Create FieldDescription from a form entity.
 */
class DynamicListFactory implements DynamicListFactoryInterface
{
    /**
     * @var string
     */
    protected $defaultBuilder;

    /**
     * @var array
     */
    protected $builders;

    /**
     * DynamicListFactory constructor.
     *
     * @param string $defaultBuilder
     */
    public function __construct($defaultBuilder)
    {
        $this->defaultBuilder = $defaultBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDescriptors(Form $form, $locale)
    {
        $fieldDescriptors = [];

        $fieldDescriptors['id'] = new FieldDescriptor(
            'id',
            'sulu_form.id',
            FieldDescriptorInterface::VISIBILITY_NO,
            '',
            'string'
        );

        foreach ($form->getFields() as $field) {
            if (in_array($field->getType(), Dynamic::$HIDDEN_TYPES)) {
                continue;
            }

            $title = '';
            $translation = $field->getTranslation($locale);

            if ($translation) {
                $title = $translation->getShortTitle() ?: strip_tags($translation->getTitle());
            }

            $fieldDescriptors[$field->getKey()] = new FieldDescriptor(
                $field->getKey(),
                $title,
                FieldDescriptorInterface::VISIBILITY_YES,
                '',
                'string',
                'false'
            );
        }

        $fieldDescriptors['created'] = new FieldDescriptor(
            'created',
            'sulu_admin.created',
            FieldDescriptorInterface::VISIBILITY_YES,
            '',
            'datetime'
        );

        return $fieldDescriptors;
    }

    /**
     * {@inheritdoc}
     */
    public function build($dynamics, $locale, $builder = 'default')
    {
        $entries = [];

        foreach ($dynamics as $dynamic) {
            foreach ($this->getBuilder($builder)->build($dynamic, $locale) as $entry) {
                $entries[] = $entry;
            }
        }

        return $entries;
    }

    /**
     * {@inheritdoc}
     */
    public function add(DynamicListBuilderInterface $builder, $alias)
    {
        $this->builders[$alias] = $builder;
    }

    /**
     * Get builder.
     *
     * @param string $alias
     *
     * @return DynamicListBuilderInterface
     *
     * @throws \Exception
     */
    protected function getBuilder($alias = null)
    {
        if (!$alias || 'default' === $alias) {
            $alias = $this->defaultBuilder;
        }

        if (!$this->builders[$alias]) {
            throw new \Exception(sprintf('Bilder with the name "%s" not found.', $alias));
        }

        return $this->builders[$alias];
    }
}
