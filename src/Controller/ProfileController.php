<?php

// src/Controller/ProfileController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

class ProfileController extends AbstractController
{
    // Route pour MON profil (édition)
    #[Route('/profile', name: 'app_profile_show')]
    #[IsGranted('ROLE_USER')]
    public function show(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $participant = $user->getParticipant();

        // Si le formulaire est soumis
        if ($request->isMethod('POST')) {
            // Récupération des données du formulaire
            $participant->setPseudo($request->request->get('pseudo'));
            $participant->setPrenom($request->request->get('prenom'));
            $participant->setNom($request->request->get('nom'));
            $participant->setTelephone($request->request->get('telephone'));
            $participant->setMail($request->request->get('email'));

            // TODO: gérer le changement de mot de passe si nécessaire

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès');

            return $this->redirectToRoute('app_profile_show');
        }

        // affiche le formulaire d'édition de mon profil
        return $this->render('profile/edit.html.twig', [
            'user' => $user,
        ]);
    }

    // Route pour voir le profil d'UN AUTRE utilisateur (lecture seule)
    #[Route('/profile/{id}', name: 'app_profile_view')]
    #[IsGranted('ROLE_USER')]
    public function view(int $id): Response
    {
        // TODO: récupérer l'utilisateur par son ID
        // affiche juste le profil en lecture seule
        return $this->render('profile/show.html.twig', [
            'user' => $this->getUser(), // à remplacer par l'utilisateur trouvé
        ]);
    }
}
