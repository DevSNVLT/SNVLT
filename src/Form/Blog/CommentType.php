<?php

namespace App\Form\Blog;

use App\Entity\Blog\ArticleBlog;
use App\Entity\Blog\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $art = new ArticleBlog ;
        $builder
            ->add('content', TextareaType::class, [
                'label'=>'votre message'
            ])
            ->add('article', HiddenType::class)
            ->add('send', SubmitType::class, [
                'label'=>'Envoyer',
                'attr'=>[
                    'class'=>'btn btn-primary'
                ]
            ])
        ;
        $builder->get('article')
            ->addModelTransformer(new CallbackTransformer(

                fn($art)    => $art->getId(),
                fn($art)    => $art->getTitle()));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'csrf_token_id'=>'comment_add'
        ]);
    }
}
