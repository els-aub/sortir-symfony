<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Participant;
use App\Entity\Site;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 🔹 On crée un site par défaut (ou tu peux en récupérer un si déjà créé dans AppFixtures)
        $site = new Site();
        $site->setNomSite("Site Principal");
        $manager->persist($site);

        // 🔹 Création du User
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password123')
        );

        // 🔹 Création du Participant lié
        $participant = new Participant();
        $participant->setPseudo("testuser");
        $participant->setNom("User");
        $participant->setPrenom("Test");
        $participant->setMail("user@test.com");
        $participant->setMotDePasse("password123"); // champ legacy, pas utilisé pour l'auth
        $participant->setSite($site);

        // Lier User <-> Participant
        $user->setParticipant($participant);

        $manager->persist($user);
        $manager->persist($participant);

        $manager->flush();
    }
}
