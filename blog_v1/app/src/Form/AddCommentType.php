<?php
/**
 * Add comment type.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * AddCommentType class.
 */
class AddCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('GET');
        $builder->add('autor', EmailType::class, ['label' => 'label.author', 'required' => true]);
        $builder->add('commentText', TextareaType::class, ['label' => 'label.comment_text', 'required' => true,
        'attr' => ['max_length' => 500], ]);
        $builder->add('save', SubmitType::class, ['label' => 'action.save']);
    }
}
