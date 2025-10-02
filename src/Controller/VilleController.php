<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

// Controleur pour gérer les villes (CRUD complet : liste, créer, voir, modifier, supprimer)
#[Route('/ville')]
#[IsGranted('ROLE_ADMIN')] // restriction d'accès aux admins uniquement
final class VilleController extends AbstractController
{
    // Liste des villes + filtre
    #[Route(name: 'app_ville_index', methods: ['GET'])]
    public function index(Request $request, VilleRepository $villeRepository): Response
    {
        // On récupère le filtre dans l'URL
        $q = $request->query->get('q', '');

        // Si un filtre est saisi → recherche par nom LIKE
        if ($q) {
            $villes = $villeRepository->createQueryBuilder('v')
                ->where('v.nomVille LIKE :q')
                ->setParameter('q', '%'.$q.'%')
                ->getQuery()
                ->getResult();
        } else {
            $villes = $villeRepository->findAll();
        }

        return $this->render('ville/index.html.twig', [
            'villes' => $villes,
            'q' => $q,
        ]);
    }

    // Créer une nouvelle ville
    #[Route('/new', name: 'app_ville_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ville/new.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/{idVille}', name: 'app_ville_show', methods: ['GET'])]
    public function show(Ville $ville): Response
    {
        return $this->render('ville/show.html.twig', [
            'ville' => $ville,
        ]);
    }

    #[Route('/{idVille}/edit', name: 'app_ville_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ville $ville, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    #[Route('/{idVille}', name: 'app_ville_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['idVille' => 'idVille'])] Ville $ville,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$ville->getIdVille(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
    }

}
