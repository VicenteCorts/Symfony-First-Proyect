<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder-> add('title', TextType::class, array(
            'label' => 'Título'
        ))
                -> add('content', TextAreaType::class, array(
            'label' => 'Contenido'
        ))
                -> add('priority', ChoiceType::class, array(
            'label' => 'Prioridad',
            'choices' => array(
                'Alta' => 'High',
                'Media' => 'Medium',
                'Baja' => 'Low',
            )
        ))
                -> add('hours', TextType::class, array(
            'label' => 'Horas Presupuestadas'
        ))
                -> add('submit', SubmitType::class, array(
            'label' => 'Crear Tarea'
        ));
    }
}