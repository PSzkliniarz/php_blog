<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('GET');
        $builder->add("autor", EmailType::class, ['label' => 'label.autor:']);
        $builder->add("comment_text", TextareaType::class, ['label' => 'label.komentarz:', 'required' => true,
            'attr' => ['max_length' => 500]]);
        $builder->add('save', SubmitType::class, array('label' => 'label.zapisz'));
    }

}