<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\HttpFoundation\JsonResponse; inutile

#[Route('/site')]
final class SiteController extends AbstractController
{
    #[Route(name: 'app_site_index', methods: ['GET'])]
    public function index(SiteRepository $siteRepository): Response
    {
        // récup tous les sites
        return $this->render('site/index.html.twig', [
            'sites' => $siteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_site_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();

            // revoir
            return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site/new.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{idSite}', name: 'app_site_show', methods: ['GET'])]
    public function show(Site $site): Response
    {
        // affiche un site
        return $this->render('site/show.html.twig', [
            'site' => $site,
        ]);
    }

    #[Route('/{idSite}/edit', name: 'app_site_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Site $site, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // update

            return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site/edit.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }

    #[Route('/{idSite}', name: 'app_site_delete', methods: ['POST'])]
    public function delete(Request $request, Site $site, EntityManagerInterface $entityManager): Response
    {
        // check du token sinon ca bug
        if ($this->isCsrfTokenValid('delete'.$site->getIdSite(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($site);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
    }
}
