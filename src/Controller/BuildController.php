<?php

namespace App\Controller;

use App\Entity\Build;
use App\Entity\BuildAsset;
use App\Entity\BuildCategory;
use App\Entity\BuildImage;
use App\Entity\BuildTag;
use App\Entity\Tag;
use App\Form\BuildType;
use App\Repository\BuildRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        if (!$build->getVisibility()) {
            $build->setVisibility('PUBLIC');
        }

        $form = $this->createForm(BuildType::class, $build);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($build);

            // Catégorie (simple)
            $category = $form->get('category')->getData();
            if ($category) {
                $buildCategory = new BuildCategory($build, $category);
                $entityManager->persist($buildCategory);
            }

            // Tags (CSV depuis input hidden)
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

            // Matériaux (collection mappée)
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
                    // ignore upload failure; validation already ran
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
            if ($worldFile) {
                $originalFilename = pathinfo($worldFile->getClientOriginalName(), PATHINFO_FILENAME);
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
                    $asset->setFilename($worldFile->getClientOriginalName());
                    $asset->setSizeBytes((int) ($worldFile->getSize() ?? 0));
                    $entityManager->persist($asset);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_build_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build/new.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_build_show', methods: ['GET'])]
    public function show(Build $build, BuildRepository $buildRepository): Response
    {

        return $this->render('build/show.html.twig', [
            'build' => $buildRepository->getBuildWithJoinByUser( $build),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_build_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Build $build, EntityManagerInterface $entityManager, TagRepository $tagRepository, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $form = $this->createForm(BuildType::class, $build, [
            'require_images' => false,
        ]);

        if (!$request->isMethod('POST')) {
            $existingCategory = $build->getBuildCategories()->first();
            if ($existingCategory) {
                $form->get('category')->setData($existingCategory->getCategory());
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
            // Catégorie: on remplace l'existante
            foreach ($build->getBuildCategories() as $existing) {
                $entityManager->remove($existing);
            }
            $category = $form->get('category')->getData();
            if ($category) {
                $entityManager->persist(new BuildCategory($build, $category));
            }

            // Tags: si rempli, on remplace
            $rawTags = (string) $form->get('tags')->getData();
            if (trim($rawTags) !== '') {
                foreach ($build->getBuildTags() as $existing) {
                    $entityManager->remove($existing);
                }

                $tagNames = array_values(array_filter(array_map(static fn($t) => trim($t), preg_split('/[\n,]+/', $rawTags) ?: [])));
                $tagNames = array_slice(array_values(array_unique($tagNames)), 0, 10);

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

                    $entityManager->persist(new BuildTag($build, $existingTag));
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
            if ($worldFile) {
                $originalFilename = pathinfo($worldFile->getClientOriginalName(), PATHINFO_FILENAME);
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
                    $asset->setFilename($worldFile->getClientOriginalName());
                    $asset->setSizeBytes((int) ($worldFile->getSize() ?? 0));
                    $entityManager->persist($asset);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_build_index', [], Response::HTTP_SEE_OTHER);
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
            $entityManager->remove($build);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_build_index', [], Response::HTTP_SEE_OTHER);
    }
}
