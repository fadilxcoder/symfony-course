<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required'  => true,
                'attr'       => [
                    'class' => "form-control"
                ]
            ])
            ->add('author',TextType::class, [
                'attr'       => [
                    'class' => "form-control"
                ]
            ])
            ->add('body', TextareaType::class, [
                'attr'       => [
                    'class' => "form-control"
                ]
            ])
            ->add('url', TextType::class, [
                'required'  => false,
                'attr'      => [
                    'placeholder'   => 'www.example.com',
                    'class' => "form-control"
                ]
            ])
            /*
            ->add('category',EntityType::class, [
                'class'         => Category::class,
                'choice_label'  => 'name',
                'expanded'      => false,
                'multiple'      => false,
                'attr'      => [
                    'class' => 'form-control'
                ]
            ])
            */
            ->add('category',EntityType::class, [
                'class'         => Category::class,
                'choice_label'  => 'name',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'expanded'      => false,
                'multiple'      => false,
                'attr'      => [
                    'class' => 'form-control'
                ]
            ])
            ->add('save', SubmitType::class, [
                'label'     => 'Save Article',
                'attr'      => [
                    'class' => "form-control btn btn-primary btn-lg float-left"
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function ($event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (null !== $data->getId()) {
                $form->add('status', ChoiceType::class, [
                    'mapped'     => false,
                    'expanded'   => false,
                    'multiple'   => false,
                    'choices'    => [
                        'Inactive' => 0,
                        'Low priority' => 1,
                        'Medium priority' => 2,
                        'High priority' => 3
                    ],
                    'attr'       => [
                        'class' => "form-control"
                    ]
                ]);
            }

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
