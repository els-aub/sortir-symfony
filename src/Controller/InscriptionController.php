<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\InscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\HttpFoundation\JsonResponse; // inutile

#[Route('/inscription')]
final class InscriptionController extends AbstractController
{
    #[Route(name: 'app_inscription_index', methods: ['GET'])]
    public function index(InscriptionRepository $inscriptionRepository): Response
    {
        // récup tout (pas de tri ici, a voir plus tard si filtre)
        $all = $inscriptionRepository->findAll();

        // var_dump($all); die(); // test fait
        return $this->render('inscription/index.html.twig', [
            'inscriptions' => $all,
        ]);
    }

    #[Route('/new', name: 'app_inscription_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $inscription = new Inscription(); // objet vide au debut
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // save bdd
            $entityManager->persist($inscription);
            $entityManager->flush();

            // je redir vers index (pe etre vers show aurait ete mieux ?) !!!!!!!
            return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscription/new.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    #[Route('/{sortie}', name: 'app_inscription_show', methods: ['GET'])]
    public function show(Inscription $inscription): Response
    {
        // normalement il trouve auto grace au param converter
        return $this->render('inscription/show.html.twig', [
            'inscription' => $inscription,
        ]);
    }

    #[Route('/{sortie}/edit', name: 'app_inscription_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InscriptionType::class, $inscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // pas besoin persist ici

            return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form' => $form,
        ]);
    }

    #[Route('/{sortie}', name: 'app_inscription_delete', methods: ['POST'])]
    public function delete(Request $request, Inscription $inscription, EntityManagerInterface $entityManager): Response
    {
        // check securité token (sinon erreur 403)
        if ($this->isCsrfTokenValid('delete'.$inscription->getSortie(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($inscription);
            $entityManager->flush();
        }

        // return new Response("inscription deleted"); // test unitaire
        return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
    }
}
