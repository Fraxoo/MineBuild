<?php

namespace App\Controller;

use App\Entity\Build;
use App\Entity\BuildAsset;
use App\Entity\BuildCategory;
use App\Entity\BuildDownload;
use App\Entity\BuildImage;
use App\Entity\BuildTag;
use App\Entity\BuildVersion;
use App\Entity\Comment;
use App\Entity\Mcversion;
use App\Entity\Tag;
use App\Form\BuildType;
use App\Form\CommentType;
use App\Repository\BuildDownloadRepository;
use App\Repository\BuildLikeRepository;
use App\Repository\BuildRepository;
use App\Repository\BuildSaveRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Exception\MissingColumnException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/build')]
final class BuildController extends AbstractController
{



    #[Route(name: 'app_build_index', methods: ['GET'])]
    public function index(BuildRepository $buildRepository): Response
    {
        return $this->render('build/index.html.twig', [
            'builds' => $buildRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_build_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, TagRepository $tagRepository, SluggerInterface $slugger): Response
    {

        $this->denyAccessUnlessGranted('ROLE_USER');


        $build = new Build();
        $user = $this->getUser();
        if ($user instanceof \App\Entity\User) {
            $build->setAuthor($user);
        }

        $form = $this->createForm(BuildType::class, $build);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($build);

            

            $category = $form->get('category')->getData();
            if ($category) {
                $buildCategory = new BuildCategory($build, $category);
                $entityManager->persist($buildCategory);
            }

            $rawTags = (string) $form->get('tags')->getData();
            $tagNames = array_values(array_filter(array_map(static fn($t) => trim($t), preg_split('/[\n,]+/', $rawTags) ?: [])));
            $tagNames = array_slice(array_values(array_unique($tagNames)), 0, 10);
            foreach ($tagNames as $tagName) {
                if ($tagName === '') {
                    continue;
                }

                $existing = $tagRepository->findOneBy(['name' => $tagName]);
                if (!$existing) {
                    $tag = new Tag();
                    $tag->setName($tagName);

                    $baseSlug = strtolower((string) $slugger->slug($tagName));
                    $baseSlug = $baseSlug ?: strtolower(bin2hex(random_bytes(6)));
                    $slug = $baseSlug;
                    if ($tagRepository->findOneBy(['slug' => $slug])) {
                        $slug = $baseSlug . '-' . strtolower(bin2hex(random_bytes(3)));
                    }

                    $tag->setSlug($slug);
                    $entityManager->persist($tag);
                    $existing = $tag;
                }

                $buildTag = new BuildTag($build, $existing);
                $entityManager->persist($buildTag);
            }

            foreach ($build->getMaterials() as $material) {
                $material->setBuild($build);
                $entityManager->persist($material);
            }

            // Upload images
            $imageFiles = $form->get('image_files')->getData();
            $sortOrder = 0;
            foreach ($imageFiles as $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = strtolower((string) $slugger->slug($originalFilename));
                $newFilename = ($safeFilename ?: 'image') . '-' . uniqid() . '.' . ($file->guessExtension() ?: 'bin');

                try {
                    $file->move($this->getParameter('build_images_directory'), $newFilename);
                } catch (FileException $e) {
                    continue;
                }

                $image = new BuildImage();
                $image->setBuild($build);
                $image->setUrl($newFilename);
                $image->setAlt($originalFilename);
                $image->setSortOrder($sortOrder++);
                $entityManager->persist($image);
            }

            $worldFile = $form->get('world_file')->getData();
            if ($worldFile instanceof UploadedFile && $worldFile->isValid()) {
                $clientOriginalName = $worldFile->getClientOriginalName();
                $sizeBytes = (int) ($worldFile->getSize() ?? 0);

                $originalFilename = pathinfo($clientOriginalName, PATHINFO_FILENAME);
                $safeFilename = strtolower((string) $slugger->slug($originalFilename));
                $newFilename = ($safeFilename ?: 'world') . '-' . uniqid() . '.' . ($worldFile->guessExtension() ?: 'bin');

                try {
                    $worldFile->move($this->getParameter('build_assets_directory'), $newFilename);
                } catch (FileException $e) {
                    // ignore upload failure
                    $newFilename = null;
                }

                if ($newFilename) {
                    $asset = new BuildAsset();
                    $asset->setBuild($build);
                    $asset->setType('world');
                    $asset->setUrl($newFilename);
                    $asset->setFilename($clientOriginalName);
                    $asset->setSizeBytes($sizeBytes);
                    $entityManager->persist($asset);
                }
            }

            $mcVersion = $form->get('Mcversion')->getData();
            if ($mcVersion) {
                $newMcVersion = new BuildVersion();
                $newMcVersion->setVersion($mcVersion);
                $newMcVersion->setBuild($build);
                $entityManager->persist($newMcVersion);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build/new.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }

    #[Route('/build/{id}/download', name: 'app_build_download')]
    public function download(Build $build, BuildDownloadRepository $buildDownloadRepository, EntityManagerInterface $entityManager): BinaryFileResponse
    {
        if ($build->getDeletedAt() !== null) {
            throw $this->createNotFoundException('Build introuvable.');
        }

        $worldAsset = null;
        foreach ($build->getAssets() as $asset) {
            if ($asset->getType() === 'world') {
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
            $downloadedByUser = $buildDownloadRepository->findOneBy([
                'build' => $build,
                'user_id' => $user,
            ]);

            if (!$downloadedByUser) {
                $entityManager->persist(new BuildDownload($build, $user));
                $build->setDownloadsCount($build->getDownloadsCount() + 1);
                $entityManager->flush();
            }
        }

        return $this->file(
            $filePath,
            $downloadFilename,
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );
    }

    #[Route('/{id}', name: 'app_build_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Build $build, EntityManagerInterface $em, BuildSaveRepository $buildSaveRepository, BuildRepository $buildRepository, BuildLikeRepository $buildLikeRepository): Response
    {
        $build = $buildRepository->getBuildWithJoinByUser($build);
        if (!$build) {
            throw $this->createNotFoundException('Build introuvable.');
        }

        $user = $this->getUser();
        $isLikedByUser = $user instanceof \App\Entity\User
            ? $buildLikeRepository->existsForBuildAndUser($build->getId(), $user->getId())
            : false;

        $isFollowedByUser = $user instanceof \App\Entity\User
            ? $build->getAuthor()->getFollowerRelations()->exists(fn($i, $rel) => $rel->getFollower()->getId() === $user->getId())
            : false;

        $isSavedByUser = $user instanceof \App\Entity\User
            ? $buildSaveRepository->findOneBy([
                'build' => $build,
                'user' => $user,
            ]) !== null
            : false;

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            if ($user instanceof \App\Entity\User) {
                $comment->setAuthor($user);
            }
            $comment->setBuild($build);
            $comment->setVisibility("PUBLIC");

            if ($form->isValid()) {
                $em->persist($comment);
                $em->flush();

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
    public function edit(Request $request, Build $build, EntityManagerInterface $entityManager, TagRepository $tagRepository, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($build->getDeletedAt() !== null) {
            throw $this->createNotFoundException('Build introuvable.');
        }

        $form = $this->createForm(BuildType::class, $build, [
            'require_images' => false,
        ]);

        if (!$request->isMethod('POST')) {
            $existingCategory = $build->getBuildCategories()->first();
            if ($existingCategory) {
                $form->get('category')->setData($existingCategory->getCategory());
            }

            $existingBuildVersion = $build->getBuildVersions()->first();
            if ($existingBuildVersion) {
                $form->get('Mcversion')->setData($existingBuildVersion->getVersion());
            }

            $existingTags = [];
            foreach ($build->getBuildTags() as $buildTag) {
                if ($buildTag->getTag() && $buildTag->getTag()->getName()) {
                    $existingTags[] = $buildTag->getTag()->getName();
                }
            }
            if ($existingTags) {
                $form->get('tags')->setData(implode(',', $existingTags));
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $deleteImageIds = $request->request->all('delete_images');
            } catch (\Symfony\Component\HttpFoundation\Exception\BadRequestException) {
                $deleteImageIds = [];
            }
            $deleteImageIds = array_fill_keys(array_map('strval', $deleteImageIds), true);

            $buildImagesDir = rtrim((string) $this->getParameter('build_images_directory'), '/');
            foreach ($build->getImages()->toArray() as $existingImage) {
                $imageId = $existingImage->getId();
                if ($imageId === null || !isset($deleteImageIds[(string) $imageId])) {
                    continue;
                }

                $filename = $existingImage->getUrl();
                if ($filename) {
                    $filePath = $buildImagesDir . '/' . $filename;
                    if (is_file($filePath)) {
                        @unlink($filePath);
                    }
                }

                $build->removeImage($existingImage);
                $entityManager->remove($existingImage);
            }

            $deleteWorldAsset = $request->request->has('delete_world_asset');

            // Catégorie: éviter remove+recreate identique (collision Doctrine sur clé composite)
            $selectedCategory = $form->get('category')->getData();
            $existingBuildCategories = $build->getBuildCategories()->toArray();
            $keepExistingCategory = false;
            $selectedCategoryId = $selectedCategory?->getId();
            foreach ($existingBuildCategories as $existing) {
                $existingCategoryId = $existing->getCategory()?->getId();
                if ($selectedCategoryId !== null && $existingCategoryId === $selectedCategoryId) {
                    $keepExistingCategory = true;
                    continue;
                }

                $entityManager->remove($existing);
            }
            if ($selectedCategory && !$keepExistingCategory) {
                $entityManager->persist(new BuildCategory($build, $selectedCategory));
            }

            $selectedMcVersion = $form->get('Mcversion')->getData();
            $existingBuildVersions = $build->getBuildVersions()->toArray();
            $buildVersion = array_shift($existingBuildVersions);

            if ($buildVersion) {
                $buildVersion->setVersion($selectedMcVersion);
            } elseif ($selectedMcVersion) {
                $buildVersion = new BuildVersion();
                $buildVersion->setBuild($build);
                $buildVersion->setVersion($selectedMcVersion);
                $entityManager->persist($buildVersion);
            }

            foreach ($existingBuildVersions as $extraBuildVersion) {
                $entityManager->remove($extraBuildVersion);
            }

            // Tags: si rempli, on synchronise (pas de remove+recreate identique)
            $rawTags = (string) $form->get('tags')->getData();
            if (trim($rawTags) !== '') {
                $tagNames = array_values(array_filter(array_map(static fn($t) => trim($t), preg_split('/[\n,]+/', $rawTags) ?: [])));
                $tagNames = array_slice(array_values(array_unique($tagNames)), 0, 10);

                $desiredTagsById = [];
                foreach ($tagNames as $tagName) {
                    if ($tagName === '') {
                        continue;
                    }

                    $existingTag = $tagRepository->findOneBy(['name' => $tagName]);
                    if (!$existingTag) {
                        $tag = new Tag();
                        $tag->setName($tagName);

                        $baseSlug = strtolower((string) $slugger->slug($tagName));
                        $baseSlug = $baseSlug ?: strtolower(bin2hex(random_bytes(6)));
                        $slug = $baseSlug;
                        if ($tagRepository->findOneBy(['slug' => $slug])) {
                            $slug = $baseSlug . '-' . strtolower(bin2hex(random_bytes(3)));
                        }

                        $tag->setSlug($slug);
                        $entityManager->persist($tag);
                        $existingTag = $tag;
                    }

                    $tagId = $existingTag->getId();
                    if ($tagId !== null) {
                        $desiredTagsById[$tagId] = $existingTag;
                    }
                }

                $existingBuildTags = $build->getBuildTags()->toArray();
                foreach ($existingBuildTags as $existing) {
                    $existingTagId = $existing->getTag()?->getId();
                    if ($existingTagId !== null && isset($desiredTagsById[$existingTagId])) {
                        unset($desiredTagsById[$existingTagId]);
                        continue;
                    }

                    $entityManager->remove($existing);
                }

                foreach ($desiredTagsById as $tag) {
                    $entityManager->persist(new BuildTag($build, $tag));
                }
            }

            // Matériaux: persist explicite (pas de cascade)
            foreach ($build->getMaterials() as $material) {
                $material->setBuild($build);
                $entityManager->persist($material);
            }

            // Upload images (optionnel)
            $imageFiles = $form->get('image_files')->getData();
            $sortOrder = 0;
            foreach ($build->getImages() as $existingImage) {
                $sortOrder = max($sortOrder, $existingImage->getSortOrder() + 1);
            }
            foreach ($imageFiles as $file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = strtolower((string) $slugger->slug($originalFilename));
                $newFilename = ($safeFilename ?: 'image') . '-' . uniqid() . '.' . ($file->guessExtension() ?: 'bin');

                try {
                    $file->move($this->getParameter('build_images_directory'), $newFilename);
                } catch (FileException $e) {
                    continue;
                }

                $image = new BuildImage();
                $image->setBuild($build);
                $image->setUrl($newFilename);
                $image->setAlt($originalFilename);
                $image->setSortOrder($sortOrder++);
                $entityManager->persist($image);
            }

            // Fichier monde (optionnel)
            $worldFile = $form->get('world_file')->getData();
            if ($worldFile instanceof UploadedFile && $worldFile->isValid()) {
                $deleteWorldAsset = true;
            }

            if ($deleteWorldAsset) {
                $buildAssetsDir = rtrim((string) $this->getParameter('build_assets_directory'), '/');
                foreach ($build->getAssets()->toArray() as $existingAsset) {
                    if ($existingAsset->getType() !== 'world') {
                        continue;
                    }

                    $filename = $existingAsset->getUrl();
                    if ($filename) {
                        $assetPath = $buildAssetsDir . '/' . $filename;
                        if (is_file($assetPath)) {
                            @unlink($assetPath);
                        }
                    }

                    $build->removeAsset($existingAsset);
                    $entityManager->remove($existingAsset);
                }
            }

            if ($worldFile instanceof UploadedFile && $worldFile->isValid()) {
                $clientOriginalName = $worldFile->getClientOriginalName();
                $sizeBytes = (int) ($worldFile->getSize() ?? 0);

                $originalFilename = pathinfo($clientOriginalName, PATHINFO_FILENAME);
                $safeFilename = strtolower((string) $slugger->slug($originalFilename));
                $newFilename = ($safeFilename ?: 'world') . '-' . uniqid() . '.' . ($worldFile->guessExtension() ?: 'bin');

                try {
                    $worldFile->move($this->getParameter('build_assets_directory'), $newFilename);
                } catch (FileException $e) {
                    $newFilename = null;
                }

                if ($newFilename) {
                    $asset = new BuildAsset();
                    $asset->setBuild($build);
                    $asset->setType('world');
                    $asset->setUrl($newFilename);
                    $asset->setFilename($clientOriginalName);
                    $asset->setSizeBytes($sizeBytes);
                    $entityManager->persist($asset);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build/edit.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_build_delete', methods: ['POST'])]
    public function delete(Request $request, Build $build, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $build->getId(), $request->getPayload()->getString('_token'))) {
            $build->setDeletedAt(new \DateTimeImmutable());

            $user = $this->getUser();
            if ($user instanceof \App\Entity\User) {
                $build->setDeletedBy($user);
            }

            $deletedReason = trim($request->getPayload()->getString('deleted_reason'));
            $build->setDeletedReason($deletedReason !== '' ? $deletedReason : null);

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }


}
