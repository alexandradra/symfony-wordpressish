<?php

namespace AppBundle\Form;

use AppBundle\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title')
                ->add('content')
                ->add('date', DateType::class, ['widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy'])
                //->add('date')
                ->add('publication', null, ['required' => false])
                ->add('image', ImageType::class, ['required' => false])
                ->add('tags', EntityType::class, [
                    'required' => false,
                    'class' => Tag::class,
                    'choice_label' => 'title',
//                    Détermine le choix (bouton radio, liste déroulante, etc.)
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function ($er) {
                        return $er->createQueryBuilder('t')
                                ->orderBy('t.title', 'ASC');
                    },
                ])
                ->add('save', SubmitType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Article'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'appbundle_article';
    }

}
