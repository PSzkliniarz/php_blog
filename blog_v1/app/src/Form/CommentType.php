<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'comment_text',
                TextType::class,
                [
                    'label' => 'labe.comment_text',
                    'required' => true,
                    'attr' => ['max_length' => 255],
                ])
            ->add('autor')
            ->add('post')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
    /**
     * Returns the prefix of the template block name for this type.
     *
     * The block prefix defaults to the underscored short class name with
     * the "Type" suffix removed (e.g. "UserProfileType" => "user_profile").
     *
     * @return string string
     */
    public function getBlockPrefix(): string
    {
        return 'comment';
    }
}
