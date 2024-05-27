<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteCreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note_name', TypeTextType::class, [
                "label" => "Nom de la note"
            ])
            ->add('note_category', ChoiceType::class, [
                'choices' => [
                    'Cours' => 'Study',
                    'Personel' => 'Personal'
                ],
                'placeholder' => 'Choissisez une catégorie',
                "label" => 'Categorie'
            ])
            ->add('note_content', TextareaType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
