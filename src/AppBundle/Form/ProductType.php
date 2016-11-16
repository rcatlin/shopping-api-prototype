<?php

namespace AppBundle\Form;

use AppBundle\Repository\RetailerRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('price', IntegerType::class, [
                'required' => false,
            ])
            ->add('retailer', EntityType::class, [
                'class' => 'AppBundle:Retailer',
                'empty_data' => null,
                'required' => false,
            ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product',
            'error_bubbling' => true,
            'invalid_message' => 'Product is invalid.',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return '';
    }
}
