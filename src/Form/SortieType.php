<?php
namespace App\Form;

use App\Entity\Sortie;
use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Colonne gauche : infos générales
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie',
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'widget' => 'single_text',
            ])
            ->add('dateCloture', DateType::class, [
                'label' => 'Date limite d’inscription',
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre de places',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (minutes)',
                'required' => false,
            ])
            ->add('descriptionInfos', TextareaType::class, [
                'label' => 'Description et infos',
                'required' => false,
            ])

            // Colonne droite : ville (unmapped) + lieu (mappé) + infos du lieu (unmapped, readonly)
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                // grâce au __toString dans Ville, pas besoin de préciser choice_label
                'label' => 'Ville',
                'mapped' => false,
                'required' => false,
                'placeholder' => 'Sélectionner une ville…',
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                // idem : utilise le __toString de Lieu (nomLieu)
                'label' => 'Lieu',
                'required' => true,
                'placeholder' => 'Sélectionner un lieu…',
                'choice_attr' => function (?Lieu $lieu) {
                    if (!$lieu) return [];
                    $ville = $lieu->getVille();
                    return [
                        'data-ville-id' => $ville ? $ville->getIdVille() : '',
                        'data-ville'    => $ville ? $ville->getNomVille() : '',
                        'data-cp'       => $ville ? $ville->getCodePostal() : '',
                        'data-rue'      => $lieu->getRue() ?? '',
                        'data-lat'      => $lieu->getLatitude() ?? '',
                        'data-lng'      => $lieu->getLongitude() ?? '',
                    ];
                },
            ])
            ->add('rue', TextType::class, [
                'label' => 'Rue',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code postal',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('latitude', TextType::class, [
                'label' => 'Latitude',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
            ])
            ->add('longitude', TextType::class, [
                'label' => 'Longitude',
                'mapped' => false,
                'required' => false,
                'disabled' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
