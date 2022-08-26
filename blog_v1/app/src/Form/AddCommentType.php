<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setMethod('GET');
        $builder->add("autor", TextareaType::class, ['label' => 'Autor:']);
        $builder->add("comment_text", TextareaType::class, ['label' => 'Komentarz:']);
        $builder->add('save', SubmitType::class, array('label' => 'Zapisz'));
    }

}