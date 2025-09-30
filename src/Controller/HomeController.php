<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\HttpFoundation\JsonResponse; // au debut pr tester

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // redirige direct vers les sorties -> pas tres propre? mais ca marche
        return $this->redirectToRoute('app_sortie_index');

        // return new Response("page home"); // test rapide
    }
}
