<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Form\EtatType;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\HttpFoundation\JsonResponse; // j'avais testé avec ca mais pas besoin

#[Route('/etat')]
final class EtatController extends AbstractController
{
    #[Route(name: 'app_etat_index', methods: ['GET'])]
    public function index(EtatRepository $etatRepository): Response
    {
        // recup tout les etats, pas filtré... (peut etre pas adapté?)
        return $this->render('etat/index.html.twig', [
            'etats' => $etatRepository->findAll(),
        ]);

        //return new Response("debug index"); // test que j’avais fait /!\
    }

    #[Route('/new', name: 'app_etat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $etat = new Etat();
        $form = $this->createForm(EtatType::class, $etat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // normalement ca sauvegarde direct
            $entityManager->persist($etat);
            $entityManager->flush();

            // j’aurais pu rediriger ailleurs mais bon…
            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etat/new.html.twig', [
            'etat' => $etat,
            'form' => $form,
        ]);
    }

    #[Route('/{idEtat}', name: 'app_etat_show', methods: ['GET'])]
    public function show(Etat $etat): Response
    {
        // j’ai oublié si ca prend l’objet direct ou si faut find()… mais ca marche
        return $this->render('etat/show.html.twig', [
            'etat' => $etat,
        ]);
    }

    #[Route('/{idEtat}/edit', name: 'app_etat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etat $etat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EtatType::class, $etat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // save direct (pas besoin persist car déjà connu)

            return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('etat/edit.html.twig', [
            'etat' => $etat,
            'form' => $form,
        ]);
    }

    #[Route('/{idEtat}', name: 'app_etat_delete', methods: ['POST'])]
    public function delete(Request $request, Etat $etat, EntityManagerInterface $entityManager): Response
    {
        // check token sinon fail
        if ($this->isCsrfTokenValid('delete'.$etat->getIdEtat(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($etat);
            $entityManager->flush();
        }

        // au cas ou : return new Response("deleted"); // test manuel

        return $this->redirectToRoute('app_etat_index', [], Response::HTTP_SEE_OTHER);
    }
}
