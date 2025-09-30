<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // NOTE: normalement si user déjà loggé, il faudrait le redir mais
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path'); // pas su quoi mettre
        // }

        // recup erreur de connexion
        $error = $authenticationUtils->getLastAuthenticationError();
        // dernier pseudo tapé
        $lastUsername = $authenticationUtils->getLastUsername();

        // fait un dump($error); pour verifier mais ca cassait la page
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // cette méthode est JAMAIS appelée direct
        throw new \LogicException('normalement intercepté par firewall => donc jamais executé');
    }
}
