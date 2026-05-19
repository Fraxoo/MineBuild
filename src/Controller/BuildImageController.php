<?php

namespace App\Controller;

use App\Entity\BuildImage;
use App\Form\BuildImageType;
use App\Repository\BuildImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/build/image')]
final class BuildImageController extends AbstractController
{
    #[Route(name: 'app_build_image_index', methods: ['GET'])]
    public function index(BuildImageRepository $buildImageRepository): Response
    {
        return $this->render('build_image/index.html.twig', [
            'build_images' => $buildImageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_build_image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $buildImage = new BuildImage();
        $form = $this->createForm(BuildImageType::class, $buildImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($buildImage);
            $entityManager->flush();

            return $this->redirectToRoute('app_build_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build_image/new.html.twig', [
            'build_image' => $buildImage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_build_image_show', methods: ['GET'])]
    public function show(BuildImage $buildImage): Response
    {
        return $this->render('build_image/show.html.twig', [
            'build_image' => $buildImage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_build_image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BuildImage $buildImage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuildImageType::class, $buildImage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_build_image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build_image/edit.html.twig', [
            'build_image' => $buildImage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_build_image_delete', methods: ['POST'])]
    public function delete(Request $request, BuildImage $buildImage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$buildImage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($buildImage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_build_image_index', [], Response::HTTP_SEE_OTHER);
    }
}
