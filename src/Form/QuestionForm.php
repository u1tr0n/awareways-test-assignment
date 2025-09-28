<?php

namespace App\Form;

use App\Dto\Form\QuestionDto;
use App\Entity\Category;
use App\Entity\Tag;
use App\Enum\QuestionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<QuestionDto>
 */
class QuestionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var QuestionDto $question */
        $question = $builder->getData();
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Question Title',
                    'autofocus' => true,
                ],
            ])
            ->add('description', TextType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Question Description',
                ],
            ])
            ->add('type', EnumType::class, [
                'class' => QuestionType::class,
                'choice_label' => 'getTitle',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'getTitle',
                'required' => false,
            ])
        ;

        if ($question->type->value === QuestionType::DRAG_AND_DROP_ANSWER->value) {
            $builder
                ->add('leftColumnTitle', TextType::class, [
                    'required' => true,
                    'mapped' => false,
                    'data' => $question->meta[0] ?? '',
                    'attr' => [
                        'placeholder' => 'Left Column Title',
                        'autofocus' => true,
                    ],
                ])
                ->add('rightColumnTitle', TextType::class, [
                    'required' => true,
                    'mapped' => false,
                    'data' => $question->meta[1] ?? '',
                    'attr' => [
                        'placeholder' => 'Right Column Title',
                        'autofocus' => true,
                    ],
                ])
            ;
        }

        $builder->add('tags', EntityType::class, [
            'class' => Tag::class,
            'choice_label' => 'title',
            'multiple' => true,
            'expanded' => true,
            'by_reference' => false,
        ])
            ->add('save', SubmitType::class, [
                'label' => 'Save Changes',
                'attr' => [
                    'class' => 'btn btn-primary w-100',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestionDto::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'question_form',
        ]);
    }
}
