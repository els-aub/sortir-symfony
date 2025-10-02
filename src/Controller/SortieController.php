<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use App\Repository\SiteRepository;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CancelSortieType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


// Toutes les routes dedans vont commencer par /sorties
#[Route('/sorties')]
class SortieController extends AbstractController
{
    // méthode affiche la liste des sorties
    // s'active quand on va sur /sorties/
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(
        Request $request,
        SortieRepository $sortieRepository,
        SiteRepository $siteRepository,
        EtatRepository $etatRepository,          // j'ajoute le repo d'Etat
        EntityManagerInterface $em               // et l'EM pour flusher si un état change
    ): Response {
        // Je récupère les filtres depuis l'url (query string), genre ?site=1&dateMin=2024-05-01
        // j’utilise $request->query->get pour prendre les choses envoyées en GET
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

        // Pour l'instant je récupère toutes les sorties direct avec findAll()
        // normalement faudrait appliquer les filtres ici /!\ à revoir
        $sorties = $sortieRepository->findAll();

        // --- Mise à jour AUTO des états selon la date du jour ---
        // Règles (conformes au sujet) :
        //  - Ouverte -> Clôturée   quand now > dateCloture
        //  - Clôturée / Ouverte -> Activité en cours quand now >= dateDebut et now < dateFin
        //  - Activité en cours -> Activité terminée quand now >= dateFin
        //  - Annulée : on NE TOUCHE PAS
        // NB : "dateFin" = dateDebut + durée (minutes). Si durée null, on prend +60 min par défaut.
        $now = new \DateTimeImmutable();
        $atLeastOneChanged = false;
        foreach ($sorties as $s) {
            if ($this->advanceEtatIfNeeded($s, $etatRepository, $now)) {
                $atLeastOneChanged = true;
            }
        }
        if ($atLeastOneChanged) {
            $em->flush(); // je sauvegarde les changements d’états en base en une seule fois
        }

        // Les sites ça sert au filtre dans la vue, je les récupère triés par nom
        $sites = $siteRepository->findBy([], ['nomSite' => 'ASC']);

        // On renvoie vers la vue index.html.twig avec les données
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sorties,
            'sites'   => $sites,
            'filters' => $filters,
        ]);
    }


    #[Route('/{idSortie}/inscrire', name:'app_sortie_inscrire', methods: ['GET'])]
    public function inscrire(
        #[MapEntity(mapping: ['idSortie' => 'idSortie'])] Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $participant = $user?->getParticipant();

        if (!$participant) {
            throw new \LogicException("Aucun participant lié à l'utilisateur connecté.");
        }

        // Vérif état : on n'accepte l'inscription que si la sortie est "Ouverte"
        if ($sortie->getEtat()->getLibelle() !== 'Ouverte') {
            $this->addFlash('error', "La sortie n'est pas ouverte aux inscriptions.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // Vérif date limite (refus si clôture dépassée)
        if ($sortie->getDateCloture() < new \DateTimeImmutable()) {
            $this->addFlash('error', "La date limite d'inscription est dépassée.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // Vérif capacité
        if ($sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()) {
            $this->addFlash('error', "Il n'y a plus de place disponible.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // Vérif déjà inscrit
        if ($sortie->getParticipants()->contains($participant)) {
            $this->addFlash('error', "Vous êtes déjà inscrit à cette sortie.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // Ajoute le participant
        $sortie->addParticipant($participant);
        $entityManager->persist($sortie); // important pour Doctrine
        $entityManager->flush();

        $this->addFlash('success', "Inscription réussie !");
        return $this->redirectToRoute('app_sortie_index');
    }


    // passer à état ouvert (publier) — seul l'organisateur peut le faire
    #[Route('/{id}/ouvrir', name: 'app_sortie_ouvrir', methods: ['GET'])]
    public function ouvrir(Sortie $sortie, EntityManagerInterface $em, EtatRepository $etatRepo): Response
    {
        $user = $this->getUser();
        $participant = $user?->getParticipant();
        if (!$participant) {
            throw $this->createAccessDeniedException("Utilisateur non relié à un participant.");
        }

        // Sécurité serveur : seul l'organisateur peut publier sa sortie
        if ($sortie->getOrganisateur()->getIdParticipant() !== $participant->getIdParticipant()) {
            $this->addFlash('error', "Seul l'organisateur peut publier cette sortie.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // On ne publie que si la sortie est encore "Créée"
        if ($sortie->getEtat()->getLibelle() !== 'Créée') {
            $this->addFlash('error', "Cette sortie n'est pas dans l'état 'Créée'.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // Vérif que la date de clôture n'est pas dépassée
        if ($sortie->getDateCloture() < new \DateTimeImmutable()) {
            $this->addFlash('error', "Impossible d’ouvrir : la date de clôture est dépassée.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // Passage à l’état Ouverte
        $etatOuverte = $etatRepo->findOneBy(['libelle' => 'Ouverte']);
        if (!$etatOuverte) {
            throw $this->createNotFoundException("État 'Ouverte' introuvable.");
        }

        $sortie->setEtat($etatOuverte);
        $em->flush();

        $this->addFlash('success', "La sortie « {$sortie->getNom()} » est maintenant ouverte !");
        return $this->redirectToRoute('app_sortie_index');
    }


    #[Route('/{idSortie}/desister', name:'app_sortie_desister', methods: ['GET'])]
    public function desister(
        #[MapEntity(mapping: ['idSortie' => 'idSortie'])] Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $participant = $user?->getParticipant();

        if (!$participant) {
            throw new \LogicException("Aucun participant lié à l'utilisateur connecté.");
        }

        // Vérifie si le participant est inscrit
        if (!$sortie->getParticipants()->contains($participant)) {
            $this->addFlash('error', "Vous n'êtes pas inscrit à cette sortie.");
            return $this->redirectToRoute('app_sortie_index');
        }

        // Retirer le participant
        $sortie->removeParticipant($participant);
        $entityManager->flush();

        $this->addFlash('success', "Vous vous êtes désisté de la sortie « {$sortie->getNom()} ».");
        return $this->redirectToRoute('app_sortie_index');
    }



    //  créer une nouvelle sortie
    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository
    ): Response {
        // je crée un objet sortie vide
        $sortie = new Sortie();

        // je génère un formulaire lié à l’entité sortie
        $form = $this->createForm(SortieType::class, $sortie);

        // handleRequest lit les infos envoyées en POST (si le form est soumis)
        $form->handleRequest($request);

        // Si formulaire soumis et valide => je traite
        if ($form->isSubmitted() && $form->isValid()) {
            // je récup le user connecté avec $this->getUser()
            $user = $this->getUser();
            $participant = $user?->getParticipant();

            if ($participant === null) {
                throw new \LogicException('Aucun Participant lié à cet utilisateur.');
            }

            // J’associe le participant comme organisateur de la sortie
            $sortie->setOrganisateur($participant);

            // Différencier selon le bouton cliqué
            if ($request->request->has('publish')) {
                // Bouton "Publier la sortie" cliqué
                $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
            } else {
                // Bouton "Enregistrer" cliqué
                $etat = $etatRepository->findOneBy(['libelle' => 'Créée']);
            }

            if (!$etat) {
                throw new \LogicException('Etat non trouvé en base.');
            }
            $sortie->setEtat($etat);

            // Sauvegarde
            $entityManager->persist($sortie);
            $entityManager->flush();

            // après avoir saved je redirige vers la liste des sorties
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        // Si pas encore soumis ou si pas valide, j’affiche juste le form
        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }


    // afficher une sortie (détail d’une seule sortie)
    #[Route('/{idSortie}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(#[MapEntity(mapping: ['idSortie' => 'idSortie'])] Sortie $sortie): Response
    {
        // Symfony injecte directement la sortie en paramètre (pas besoin de find())
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }


    // modifier une sortie déjà existante
    #[Route('/{idSortie}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['idSortie' => 'idSortie'])] Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response {
        // je génère le form avec l’entité sortie existante (préremplie)
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Comme l’objet est déjà en base, pas besoin de persist
            // juste un flush pour sauver les modifs
            $entityManager->flush();
            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }


    // Supprimer sortie
    #[Route('/{idSortie}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['idSortie' => 'idSortie'])] Sortie $sortie,
        EntityManagerInterface $entityManager
    ): Response {
        // Vérif du token csrf, obligatoire sinon ça ne supprime pas
        if ($this->isCsrfTokenValid('delete'.$sortie->getIdSortie(), $request->request->get('_token'))) {
            // Suppression et flush
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        // après suppr on retourne sur la liste
        return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
    }


    //                                     Méthode private : met à jour état sortie si nécessaire
    // Retourne true si l'état a changé (utile pour savoir si on doit flush)
    private function advanceEtatIfNeeded(Sortie $s, EtatRepository $etatRepo, \DateTimeImmutable $now): bool
    {
        $lib = $s->getEtat()->getLibelle();

        //cas annulée :on ne bouge pas
        if ($lib === 'Annulée') {
            return false;
        }
        //calcule date de fin = dateDebut +durée ;  si durée null -> +60 min par défaut
        $dateDebut = \DateTimeImmutable::createFromMutable((clone $s->getDateDebut()));
        $dureeMin  = $s->getDuree() ?? 60;
        //$dureeMin  = $s->getDuree() ?? 60;
        $dateFin   = $dateDebut->modify('+' . $dureeMin . ' minutes');

        // 1) Si l'activité est terminée (now >= fin) -> "Activité terminée"
        if ($now >= $dateFin && $lib !== 'Activité terminée') {
            $etat = $etatRepo->findOneBy(['libelle' => 'Activité terminée']);
            if ($etat) { $s->setEtat($etat); return true; }
            return false;
        }
        // 2)si  est en cours (debut <= now < fin)-> Activité en cours
        if ($now >= $dateDebut && $now < $dateFin && $lib !== 'Activité en cours') {
            $etat = $etatRepo->findOneBy(['libelle' => 'Activité en cours']);
            if ($etat) { $s->setEtat($etat); return true; }
            return false;
        }
        // 3)si on a dépassé la clôture et qu'elle était encore ouverte -> clôturée
        // revoir pour passée
        if ($lib === 'Ouverte' && $s->getDateCloture() < $now) {
            $etat = $etatRepo->findOneBy(['libelle' => 'Clôturée']);
            if ($etat) { $s->setEtat($etat); return true; }
            return false;
        }

        //4)si on a xxxx -> passée

        // Sinon rien à faire
        return false;
    }

    // Annuler une sortie (organisateur ou admin)
    #[Route('/{idSortie}/annuler', name:'app_sortie_annuler', methods: ['GET','POST'])]
    public function annuler(
        Request $request,
        #[MapEntity(mapping: ['idSortie' => 'idSortie'])] Sortie $sortie,
        EntityManagerInterface $em,
        EtatRepository $etatRepo
    ): Response {
        $user = $this->getUser();
        $participant = $user?->getParticipant();

        if (!$participant) {
            throw $this->createAccessDeniedException("Utilisateur non relié à un participant.");
        }

        // Vérif date : pas déjà commencée
        if ($sortie->getDateDebut() <= new \DateTimeImmutable()) {
            $this->addFlash('error', "Impossible d’annuler : la sortie a déjà commencé.");
            return $this->redirectToRoute('app_sortie_show', ['idSortie' => $sortie->getIdSortie()]);
        }

        // Vérif droit : organisateur ou admin
        if ($sortie->getOrganisateur()->getIdParticipant() !== $participant->getIdParticipant() &&
            !$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException("Vous n'avez pas le droit d’annuler cette sortie.");
        }

        // Création form annulation
        $form = $this->createForm(CancelSortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Passage à l’état Annulée
            $etatAnnulee = $etatRepo->findOneBy(['libelle' => 'Annulée']);
            if (!$etatAnnulee) {
                throw $this->createNotFoundException("État 'Annulée' introuvable.");
            }
            $sortie->setEtat($etatAnnulee);

            $em->flush();

            $this->addFlash('success', "La sortie a été annulée avec succès.");
            return $this->redirectToRoute('app_sortie_index');
        }

        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }


}


