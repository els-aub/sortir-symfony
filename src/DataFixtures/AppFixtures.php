<?php

namespace App\DataFixtures;

use App\Entity\Ville;
use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // --- Villes ---
        $nantes = new Ville();
        $nantes->setNomVille('Nantes');
        $nantes->setCodePostal('44000');
        $manager->persist($nantes);

        $paris = new Ville();
        $paris->setNomVille('Paris');
        $paris->setCodePostal('75000');
        $manager->persist($paris);

        // --- Lieux ---
        $procé = new Lieu();
        $procé->setNomLieu('Parc de Procé');
        $procé->setRue('Rue des Dervallières');
        $procé->setLatitude(47.2173);
        $procé->setLongitude(-1.5534);
        $procé->setVille($nantes);
        $manager->persist($procé);

        $tourEiffel = new Lieu();
        $tourEiffel->setNomLieu('Tour Eiffel');
        $tourEiffel->setRue('Champ de Mars');
        $tourEiffel->setLatitude(48.8584);
        $tourEiffel->setLongitude(2.2945);
        $tourEiffel->setVille($paris);
        $manager->persist($tourEiffel);

        $manager->flush();
    }
}
