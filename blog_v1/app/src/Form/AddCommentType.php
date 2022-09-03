<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class AddCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('GET');
        $builder->add('autor', EmailType::class, ['label' => 'label.autor:']);
        $builder->add('comment_text', TextareaType::class, ['label' => 'label.komentarz:', 'required' => true,
            'attr' => ['max_length' => 500], ]);
        $builder->add('save', SubmitType::class, ['label' => 'label.zapisz']);
    }
}
