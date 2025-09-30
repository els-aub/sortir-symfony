<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// hésité à mettre use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/lieu')]
final class LieuController extends AbstractController
{
    #[Route(name: 'app_lieu_index', methods: ['GET'])]
    public function index(LieuRepository $lieuRepository): Response
    {
        // récupère tous les lieux (pas de pagination ici)
        $lieux = $lieuRepository->findAll();

        return $this->render('lieu/index.html.twig', [
            'lieus' => $lieux, // orthographe ? jsp, twig semble utiliser ca, à revoir ?
        ]);
    }

    #[Route('/new', name: 'app_lieu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
        }

        // test retour rapide
        // return new Response("new lieu ok");

        return $this->render('lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    #[Route('/{idLieu}', name: 'app_lieu_show', methods: ['GET'])]
    public function show(Lieu $lieu): Response
    {
        // affiche juste un seul lieu
        return $this->render('lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    #[Route('/{idLieu}/edit', name: 'app_lieu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // save direct

            return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form,
        ]);
    }

    #[Route('/{idLieu}', name: 'app_lieu_delete', methods: ['POST'])]
    public function delete(Request $request, Lieu $lieu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getIdLieu(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($lieu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lieu_index', [], Response::HTTP_SEE_OTHER);
    }
}
