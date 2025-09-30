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
        //  Créer un site par défaut
        $site = new Site();
        $site->setNomSite("Site Principal");
        $manager->persist($site);

        // =============
        // USER NORMAL 1
        // ==========
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'password123') // !!!!!!!!!!!!!!!!!!!!!! mauvaise pratique
        );

        $participant = new Participant();
        $participant->setPseudo("testuser");
        $participant->setNom("User");
        $participant->setPrenom("Test");
        $participant->setMail("user@test.com");
        $participant->setAdministrateur(false);
        $participant->setActif(true);
        $participant->setSite($site);

        $user->setParticipant($participant);

        $manager->persist($user);
        $manager->persist($participant);

        // =============
        // USER NORMAL 2
        // ==========

        // pour voir si User2 peut modifier /supprimer la sortiée crée par user 1

        $user2 = new User();
        $user2->setEmail('user2@test.com');
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'password123') // !!!!!!!!!!!!!!!!!!!!!! mauvaise pratique
        );

        $participant2 = new Participant();
        $participant2->setPseudo("testuser2");
        $participant2->setNom("User");
        $participant2->setPrenom("TestTest");
        $participant2->setMail("user2@test.com");
        $participant2->setAdministrateur(false);
        $participant2->setActif(true);
        $participant2->setSite($site);

        $user2->setParticipant($participant2);

        $manager->persist($user2);
        $manager->persist($participant2);

        // =============
        // USER NORMAL 3
        // ==========

        // A FAIRE
        // le mettre en INACTIF, pour voir s'il peut se connecter
        //



        // =================
        //  ADMIN
        // =====================
        $adminUser = new User();
        $adminUser->setEmail('admin@test.com');
        // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $adminUser->setPassword('$2y$13$Hl/l6QGfIGA5nlGC7AeJPOYODgVnqSaqCBl.UMXy1imZtHgtg9u9K');

        $adminParticipant = new Participant();
        $adminParticipant->setPseudo("superadmin");
        $adminParticipant->setNom("Admin");
        $adminParticipant->setPrenom("Super");
        $adminParticipant->setMail("admin@test.com");
        $adminParticipant->setAdministrateur(true); // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $adminParticipant->setActif(true); // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        $adminParticipant->setSite($site);

        $adminUser->setParticipant($adminParticipant);

        $manager->persist($adminUser);
        $manager->persist($adminParticipant);

        $manager->flush();
    }
}
