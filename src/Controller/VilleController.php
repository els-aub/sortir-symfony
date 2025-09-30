<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Controleur pour gérer les villes (CRUD complet : liste, créer, voir, modifier, supprimer)
#[Route('/ville')]
final class VilleController extends AbstractController
{
    // Liste des villes
    #[Route(name: 'app_ville_index', methods: ['GET'])]
    public function index(VilleRepository $villeRepository): Response
    {
        // findAll() = récupère toutes les villes de la table ville
        $villes = $villeRepository->findAll();

        // On envoie ça dans la vue index.html.twig
        return $this->render('ville/index.html.twig', [
            'villes' => $villes,
        ]);
    }

    // Créer une nouvelle ville
    #[Route('/new', name: 'app_ville_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // On instancie un objet Ville vide
        $ville = new Ville();

        // On génère le form lié à l’entité Ville
        $form = $this->createForm(VilleType::class, $ville);

        // On lie la requête au formulaire => ça remplit $ville avec les données POST si envoyées
        $form->handleRequest($request);

        // Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist = dire à Doctrine qu’on veut sauvegarder en base
            $entityManager->persist($ville);
            // flush = envoi réel dans la base SQL
            $entityManager->flush();

            // Une fois ajouté, on retourne à la liste des villes
            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        // Si on arrive ici => soit GET (form vide), soit POST mais non valide
        return $this->render('ville/new.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    // Voir le détail d’une ville
    // route : /ville/{idVille}
    #[Route('/{idVille}', name: 'app_ville_show', methods: ['GET'])]
    public function show(Ville $ville): Response
    {
        // Grâce au ParamConverter de Symfony, pas besoin de faire find()
        // Il récupère directement la Ville par son id et la passe en argument
        return $this->render('ville/show.html.twig', [
            'ville' => $ville,
        ]);
    }

    // Modifier une ville
    #[Route('/{idVille}/edit', name: 'app_ville_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ville $ville, EntityManagerInterface $entityManager): Response
    {
        // Le form est généré avec une Ville déjà existante (préremplie)
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        // Si soumis + valide => on enregistre les modifs
        if ($form->isSubmitted() && $form->isValid()) {
            // Ici pas besoin de persist car l’objet est déjà suivi par Doctrine
            $entityManager->flush();

            // Après update => retour liste
            return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
        }

        // Si pas soumis ou pas valide => on réaffiche le form
        return $this->render('ville/edit.html.twig', [
            'ville' => $ville,
            'form' => $form,
        ]);
    }

    // Supprimer une ville
    #[Route('/{idVille}', name: 'app_ville_delete', methods: ['POST'])]
    public function delete(Request $request, Ville $ville, EntityManagerInterface $entityManager): Response
    {
        // Vérif que le token CSRF correspond
        // sinon la suppression n’est pas autorisée (sécurité contre failles)
        if ($this->isCsrfTokenValid('delete'.$ville->getIdVille(), $request->getPayload()->getString('_token'))) {
            // remove = supprimer l’entité
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        // Une fois supprimée, on renvoie à la liste
        return $this->redirectToRoute('app_ville_index', [], Response::HTTP_SEE_OTHER);
    }
}
