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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
#[Route('/site')]
#[IsGranted('ROLE_ADMIN')] // restriction d'accès aux admins uniquement
final class SiteController extends AbstractController
{
    #[Route(name: 'app_site_index', methods: ['GET'])]
    public function index(Request $request, SiteRepository $siteRepository): Response
    {
        $q = $request->query->get('q', '');

        if ($q) {
            $sites = $siteRepository->createQueryBuilder('s')
                ->where('s.nomSite LIKE :q')
                ->setParameter('q', '%'.$q.'%')
                ->getQuery()
                ->getResult();
        } else {
            $sites = $siteRepository->findAll();
        }

        return $this->render('site/index.html.twig', [
            'sites' => $sites,
            'q' => $q,
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
            $entityManager->flush();
            return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('site/edit.html.twig', [
            'site' => $site,
            'form' => $form,
        ]);
    }



    #[Route('/{idSite}', name: 'app_site_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        #[MapEntity(mapping: ['idSite' => 'idSite'])] Site $site,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$site->getIdSite(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($site);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_site_index', [], Response::HTTP_SEE_OTHER);
    }

}
