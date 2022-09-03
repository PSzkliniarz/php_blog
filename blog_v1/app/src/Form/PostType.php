<?php
/**
 * Post type.
 */

namespace App\Form;

use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * PostType class
 */
class PostType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'title',
            TextType::class,
            [
                'label' => 'label.title',
                'required' => true,
                'attr' => ['max_length' => 120],
            ]
        );

        $builder->add(
            'content',
            TextType::class,
            [
                'label' => 'label.content',
                'required' => true,
                'attr' => ['max_length' => 65000],
            ]
        );

        $builder->add(
            'category',
            EntityType::class,
            [
                'label' => 'title.category',
                'class' => "App\Entity\Category",
                'placeholder' => 'title.category',
                'choice_label' => 'name',
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'post';
    }
}
