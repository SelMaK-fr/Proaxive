<?php
declare(strict_types=1);
namespace App\Type;

use App\Type;
use Palmtree\Form\Form;
use Palmtree\Form\FormBuilder;
use Palmtree\Form\Type\CheckboxType;
use Palmtree\Form\Type\TextType;

class TypeEquipmentType extends Type
{
    public function createFormBuilder(string|\stdClass|null|array $data = null): Form
    {
        $builder = (new FormBuilder('type_equipment', $data))
            ->add('name', TextType::class, [
                'label' => "Nom"
            ])
            ->add('slug', TextType::class, [
                'label' => "Slug",
                'required' => false
            ])
            ->add('is_peripheral', CheckboxType::class, [
                'required' => false,
                'label' => 'Catégorie pour périphérique'
            ])
            ;
        return $builder->getForm();
    }
}