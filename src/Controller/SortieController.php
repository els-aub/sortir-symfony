<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Repository\SiteRepository;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sorties')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(Request $request, SortieRepository $sortieRepository, SiteRepository $siteRepository): Response
    {
        // filtres alignés avec la vue
        $filters = [
            'site'         => $request->query->get('site', ''),
            'q'            => $request->query->get('q', ''),
            'dateMin'      => $request->query->get('dateMin', ''),
            'dateMax'      => $request->query->get('dateMax', ''),
            'organisateur' => $request->query->getBoolean('organisateur', false),
            'inscrit'      => $request->query->getBoolean('inscrit', false),
            'nonInscrit'   => $request->query->getBoolean('nonInscrit', false),
            'passees'      => $request->query->getBoolean('passees', false),
        ];

        // pour l’instant findAll(), à remplacer plus tard par une méthode filtrée
        $sorties = $sortieRepository->findAll();

        $sites = $siteRepository->findBy([], ['nomSite' => 'ASC']);

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'sites'   => $sites,
            'filters' => $filters,
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository // <-- injection du repo
    ): Response {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser(); // instance de User connecté
            $participant = $user?->getParticipant();

            if ($participant === null) {
                throw new \LogicException('Aucun Participant lié à cet utilisateur.');
            }

            $sortie->setOrganisateur($participant);

            // récupérer un Etat par défaut, par ex. "Créée"
            $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
            if (!$etat) {
                throw new \LogicException('Aucun Etat "Créée" trouvé en base.');
            }
            $sortie->setEtat($etat);

            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }
}


