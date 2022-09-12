<?php
/**
 * Comment type.
 */

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CommentType class.
 */
class CommentType extends AbstractType
{
    /**
     * Comment build form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'commentText',
                TextType::class,
                [
                    'label' => 'label.$commentText',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ]
            )
            ->add('autor')
            ->add('post')
        ;
    }

    /**
     * Configure Options
     *
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }

    /**
     * Get Block Prefix
     *
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'comment';
    }
}
