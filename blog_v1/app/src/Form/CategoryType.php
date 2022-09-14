<?php
/**
 * Category type.
 */

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * CategoryType class.
 */
class CategoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder param
     * @param array                $options param
     *
     * @return void return
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'attr' => ['max_length' => 255],
            ]
        );
    }

    /**
     * @param OptionsResolver $resolver param
     *
     * @return void return
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }

    /**
     * @return string return
     */
    public function getBlockPrefix(): string
    {
        return 'category';
    }
}
