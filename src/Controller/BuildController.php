<?php

namespace App\Controller;

use App\Entity\Build;
use App\Entity\Comment;
use App\Enum\BuildAssetType;
use App\Enum\Visibility;
use App\Form\BuildType;
use App\Form\CommentType;
use App\Service\BuildService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/build')]
final class BuildController extends AbstractController
{



    #[Route(name: 'app_build_index', methods: ['GET'])]
    public function index(BuildService $buildService): Response
    {
        return $this->render('build/index.html.twig', [
            'builds' => $buildService->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_build_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BuildService $buildService): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');


        $build = new Build();
        $user = $this->getUser();
        $build->setAuthor($user);

        $form = $this->createForm(BuildType::class, $build);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $buildService->create($build, $form);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        $response = $form->isSubmitted()
            ? new Response('', Response::HTTP_UNPROCESSABLE_ENTITY)
            : null;

        return $this->render('build/new.html.twig', [
            'build' => $build,
            'form' => $form,
        ], $response);
    }

    #[Route('/build/{id}/download', name: 'app_build_download')]
    public function download(Build $build, BuildService $buildService): BinaryFileResponse
    {
        if ($build->getDeletedAt() !== null) {
            throw $this->createNotFoundException('Build introuvable.');
        }

        $worldAsset = null;
        foreach ($build->getAssets() as $asset) {
            if ($asset->getType() === BuildAssetType::WORLD) {
                $worldAsset = $asset;
                break;
            }
        }

        if (!$worldAsset) {
            throw $this->createNotFoundException('Aucun fichier à télécharger.');
        }

        $storedFilename = (string) $worldAsset->getUrl();
        $downloadFilename = (string) ($worldAsset->getFilename() ?: $storedFilename);

        $filePath = rtrim((string) $this->getParameter('build_assets_directory'), '/') . '/' . $storedFilename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Fichier introuvable.');
        }

        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
            $buildService->registerDownload($build, $user);
        }

        return $this->file(
            $filePath,
            $downloadFilename,
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );
    }

    #[Route('/{id}', name: 'app_build_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Build $build, BuildService $buildService): Response
    {
        $build = $buildService->getBuildWithJoinByUser($build);

        if ($build->getVisibility() === Visibility::HIDDEN or !$build) {
            return $this->redirectToRoute('app_build_not_found', [], Response::HTTP_SEE_OTHER);
        }

        $user = $this->getUser();
        $appUser = $user instanceof \App\Entity\User ? $user : null;

        if ($request->isMethod('GET')) {
            $buildService->recordView($build, $appUser, $request);
        }

        $isLikedByUser = $buildService->isLikedByUser($build, $appUser);
        $isFollowedByUser = $buildService->isFollowedByUser($build, $appUser);
        $isSavedByUser = $buildService->isSavedByUser($build, $appUser);

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            if ($form->isValid()) {
                $buildService->addComment($build, $comment, $appUser);

                return $this->redirectToRoute('app_build_show', ['id' => $build->getId()], Response::HTTP_SEE_OTHER);
            }
        }


        return $this->render('build/show.html.twig', [
            'isSavedByUser' => $isSavedByUser,
            'isLikedByUser' => $isLikedByUser,
            'isFollowedByUser' => $isFollowedByUser,
            'build' => $build,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_build_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Build $build, BuildService $buildService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($build->getDeletedAt() !== null) {
            throw $this->createNotFoundException('Build introuvable.');
        }

        $form = $this->createForm(BuildType::class, $build, [
            'require_images' => false,
        ]);

        if (!$request->isMethod('POST')) {
            $buildService->prepareEditForm($build, $form);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $buildService->update($build, $form, $request);

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build/edit.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_build_delete', methods: ['POST'])]
    public function delete(Request $request, Build $build, BuildService $buildService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        if (!$user instanceof \App\Entity\User || $build->getAuthor()?->getId() !== $user->getId()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que vos propres builds.');
        }

        if ($this->isCsrfTokenValid('delete' . $build->getId(), $request->getPayload()->getString('_token'))) {
            $buildService->softDelete($build, $user, $request->getPayload()->getString('deleted_reason'));
        }

        $redirectTo = $request->request->get('_redirect_to');
        if (is_string($redirectTo) && str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, '//')) {
            return $this->redirect($redirectTo, Response::HTTP_SEE_OTHER);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }


}
