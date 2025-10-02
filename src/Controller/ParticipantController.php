<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/participant')]
final class ParticipantController extends AbstractController
{
    #[Route(name: 'app_participant_index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        // affiche la liste de tous les participants (attention ca peut etre lourd si bcp users)
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_participant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($participant);
            $entityManager->flush();

            // note: redirige vers index (peut-être vers "show" c’est plus logique ?)
            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    #[Route(
        '/{idParticipant}',
        name: 'app_participant_show',
        methods: ['GET'],
        requirements: ['idParticipant' => '\d+']
    )]
    public function show(
        #[MapEntity(mapping: ['idParticipant' => 'idParticipant'])] Participant $participant
    ): Response {
        // affiche la fiche profil d’un participant (vue profile/show.html.twig)
        return $this->render('profile/show.html.twig', [
            'participant' => $participant,
        ]);
    }


    #[Route(
        '/{idParticipant}/edit',
        name: 'app_participant_edit',
        methods: ['GET', 'POST'],
        requirements: ['idParticipant' => '\d+']
    )]
    public function edit(
        Request $request,
        #[MapEntity(mapping: ['idParticipant' => 'idParticipant'])] Participant $participant,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    #[Route(
        '/{idParticipant}',
        name: 'app_participant_delete',
        methods: ['POST'],
        requirements: ['idParticipant' => '\d+']
    )]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['idParticipant' => 'idParticipant'])] Participant $participant,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$participant->getIdParticipant(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    }
}
