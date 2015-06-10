<?php

namespace Client\Bundle\FormBundle\Form\Type;

use Client\Bundle\FormBundle\Entity\Example;
use Symfony\Component\Form\FormBuilderInterface;

class ExampleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->setData(new Example());

        $builder->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('email', 'text')
            ->add('option', 'choice', array(
                'choices' =>preg_split('/\r\n|\r|\n/',  $this->getAttribute('options'))
            ))
            ->add('submit', 'submit');
    }

    public function getDataClass()
    {
        return 'Client\Bundle\FormBundle\Entity\Example';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form_example';
    }
}