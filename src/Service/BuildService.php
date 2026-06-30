<?php

namespace App\Service;

use App\Entity\Build;
use App\Entity\BuildAsset;
use App\Entity\BuildCategory;
use App\Entity\BuildDownload;
use App\Entity\BuildImage;
use App\Entity\BuildTag;
use App\Entity\BuildVersion;
use App\Entity\BuildView;
use App\Entity\Comment;
use App\Entity\Tag;
use App\Entity\User;
use App\Enum\BuildAssetType;
use App\Enum\Visibility;
use App\Repository\BuildDownloadRepository;
use App\Repository\BuildLikeRepository;
use App\Repository\BuildRepository;
use App\Repository\BuildSaveRepository;
use App\Repository\BuildViewRepository;
use App\Repository\TagRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

final readonly class BuildService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BuildRepository $buildRepository,
        private TagRepository $tagRepository,
        private BuildDownloadRepository $buildDownloadRepository,
        private BuildLikeRepository $buildLikeRepository,
        private BuildSaveRepository $buildSaveRepository,
        private BuildViewRepository $buildViewRepository,
        private SluggerInterface $slugger,
        private ParameterBagInterface $parameterBag,
    ) {
    }

    /**
     * @return Build[]
     */
    public function findAll(): array
    {
        return $this->buildRepository->findAll();
    }

    public function getBuildWithJoinByUser(Build $build): ?Build
    {
        return $this->buildRepository->getBuildWithJoinByUser($build);
    }

    public function isLikedByUser(Build $build, ?User $user): bool
    {
        return $user instanceof User
            ? $this->buildLikeRepository->existsForBuildAndUser($build->getId(), $user->getId())
            : false;
    }

    public function isSavedByUser(Build $build, ?User $user): bool
    {
        return $user instanceof User
            ? $this->buildSaveRepository->findOneBy([
                'build' => $build,
                'user' => $user,
            ]) !== null
            : false;
    }

    public function isFollowedByUser(Build $build, ?User $user): bool
    {
        return $user instanceof User
            ? $build->getAuthor()->getFollowerRelations()->exists(fn($i, $rel) => $rel->getFollower()->getId() === $user->getId())
            : false;
    }

    public function create(Build $build, FormInterface $form): void
    {
        $this->entityManager->persist($build);
        $this->persistCategory($build, $form);
        $this->persistTagsFromCommaList($build, (string) $form->get('tags')->getData());
        $this->persistMaterials($build);
        $this->uploadImages($build, $form->get('image_files')->getData(), 0);
        $this->uploadWorldAsset($build, $form->get('world_file')->getData());
        $this->persistMcVersion($build, $form);

        $this->entityManager->flush();
    }

    public function prepareEditForm(Build $build, FormInterface $form): void
    {
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
            $tagName = $buildTag->getTag()?->getName();
            if ($tagName) {
                $existingTags[] = $tagName;
            }
        }

        $form->get('tags')->setData(implode(',', $existingTags));
    }

    public function update(Build $build, FormInterface $form, Request $request): void
    {
        try {
            $deleteImageIds = $request->request->all('delete_images');
        } catch (\Symfony\Component\HttpFoundation\Exception\BadRequestException) {
            $deleteImageIds = [];
        }

        $this->deleteSelectedImages($build, array_fill_keys(array_map('strval', $deleteImageIds), true));

        $deleteWorldAsset = $request->request->has('delete_world_asset');
        $this->syncCategory($build, $form);
        $this->syncMcVersion($build, $form);
        $this->syncTags($build, (string) $form->get('tags')->getData());
        $this->persistMaterials($build);

        $sortOrder = 0;
        foreach ($build->getImages() as $existingImage) {
            $sortOrder = max($sortOrder, $existingImage->getSortOrder() + 1);
        }
        $this->uploadImages($build, $form->get('image_files')->getData(), $sortOrder);

        $worldFile = $form->get('world_file')->getData();
        if ($worldFile instanceof UploadedFile && $worldFile->isValid()) {
            $deleteWorldAsset = true;
        }

        if ($deleteWorldAsset) {
            $this->deleteWorldAssets($build);
        }

        $this->uploadWorldAsset($build, $worldFile);
        $this->entityManager->flush();
    }

    public function registerDownload(Build $build, User $user): void
    {
        $downloadedByUser = $this->buildDownloadRepository->findOneBy([
            'build' => $build,
            'user_id' => $user,
        ]);

        if (!$downloadedByUser) {
            $this->entityManager->persist(new BuildDownload($build, $user));
            $build->setDownloadsCount($build->getDownloadsCount() + 1);
            $this->entityManager->flush();
        }
    }

    public function addComment(Build $build, Comment $comment, ?User $user): void
    {
        if ($user instanceof User) {
            $comment->setAuthor($user);
        }

        $comment->setBuild($build);
        $comment->setVisibility(Visibility::PUBLIC);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    public function recordView(Build $build, ?User $user, Request $request): void
    {
        if ($user instanceof User && $build->getAuthor()?->getId() === $user->getId()) {
            return;
        }

        $since = new DateTimeImmutable('-24 hours');

        if ($user instanceof User) {
            if ($this->buildViewRepository->hasRecentUserView($build, $user, $since)) {
                return;
            }

            $view = new BuildView();
            $view->setUserId($user);
        } else {
            $ipHash = hash_hmac(
                'sha256',
                $request->getClientIp() ?? 'unknown',
                (string) $this->parameterBag->get('kernel.secret')
            );

            if ($this->buildViewRepository->hasRecentIpView($build, $ipHash, $since)) {
                return;
            }

            $view = new BuildView();
            $view->setIpHash($ipHash);
        }

        $view->setBuildId($build);
        $view->setViewedAt(new DateTimeImmutable());
        $build->setViewsCount($build->getViewsCount() + 1);

        $this->entityManager->persist($view);
        $this->entityManager->flush();
    }

    public function softDelete(Build $build, User $user, string $deletedReason): void
    {
        $build->setDeletedAt(new \DateTimeImmutable());
        $build->setDeletedBy($user);
        $deletedReason = trim($deletedReason);
        $build->setDeletedReason($deletedReason !== '' ? $deletedReason : null);

        $this->entityManager->flush();
    }

    private function persistCategory(Build $build, FormInterface $form): void
    {
        $category = $form->get('category')->getData();
        if ($category) {
            $this->entityManager->persist(new BuildCategory($build, $category));
        }
    }

    private function persistTagsFromCommaList(Build $build, string $rawTags): void
    {
        $tags = array_filter(array_map('trim', explode(',', $rawTags)));

        foreach ($tags as $tag) {
            $existing = $this->findOrCreateTag($tag);
            $this->entityManager->persist(new BuildTag($build, $existing));
        }
    }

    private function persistMaterials(Build $build): void
    {
        foreach ($build->getMaterials() as $material) {
            $material->setBuild($build);
            $this->entityManager->persist($material);
        }
    }

    private function uploadImages(Build $build, mixed $imageFiles, int $sortOrder): void
    {
        if (!is_iterable($imageFiles)) {
            return;
        }

        foreach ($imageFiles as $file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = strtolower((string) $this->slugger->slug($originalFilename));
            $newFilename = ($safeFilename ?: 'image') . '-' . uniqid() . '.' . ($file->guessExtension() ?: 'bin');

            try {
                $file->move($this->getBuildImagesDirectory(), $newFilename);
            } catch (FileException) {
                continue;
            }

            $image = new BuildImage();
            $image->setBuild($build);
            $image->setUrl($newFilename);
            $image->setAlt($originalFilename);
            $image->setSortOrder($sortOrder++);
            $this->entityManager->persist($image);
        }
    }

    private function uploadWorldAsset(Build $build, mixed $worldFile): void
    {
        if (!$worldFile instanceof UploadedFile || !$worldFile->isValid()) {
            return;
        }

        $clientOriginalName = $worldFile->getClientOriginalName();
        $sizeBytes = (int) ($worldFile->getSize() ?? 0);

        $originalFilename = pathinfo($clientOriginalName, PATHINFO_FILENAME);
        $safeFilename = strtolower((string) $this->slugger->slug($originalFilename));
        $newFilename = ($safeFilename ?: 'world') . '-' . uniqid() . '.' . ($worldFile->guessExtension() ?: 'bin');

        try {
            $worldFile->move($this->getBuildAssetsDirectory(), $newFilename);
        } catch (FileException) {
            $newFilename = null;
        }

        if ($newFilename) {
            $asset = new BuildAsset();
            $asset->setBuild($build);
            $asset->setType(BuildAssetType::WORLD);
            $asset->setUrl($newFilename);
            $asset->setFilename($clientOriginalName);
            $asset->setSizeBytes($sizeBytes);
            $this->entityManager->persist($asset);
        }
    }

    private function persistMcVersion(Build $build, FormInterface $form): void
    {
        $mcVersion = $form->get('Mcversion')->getData();
        if ($mcVersion) {
            $newMcVersion = new BuildVersion();
            $newMcVersion->setVersion($mcVersion);
            $newMcVersion->setBuild($build);
            $this->entityManager->persist($newMcVersion);
        }
    }

    private function deleteSelectedImages(Build $build, array $deleteImageIds): void
    {
        $buildImagesDir = rtrim($this->getBuildImagesDirectory(), '/');
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
            $this->entityManager->remove($existingImage);
        }
    }

    private function syncCategory(Build $build, FormInterface $form): void
    {
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

            $this->entityManager->remove($existing);
        }

        if ($selectedCategory && !$keepExistingCategory) {
            $this->entityManager->persist(new BuildCategory($build, $selectedCategory));
        }
    }

    private function syncMcVersion(Build $build, FormInterface $form): void
    {
        $selectedMcVersion = $form->get('Mcversion')->getData();
        $existingBuildVersions = $build->getBuildVersions()->toArray();
        $buildVersion = array_shift($existingBuildVersions);

        if ($buildVersion) {
            $buildVersion->setVersion($selectedMcVersion);
        } elseif ($selectedMcVersion) {
            $buildVersion = new BuildVersion();
            $buildVersion->setBuild($build);
            $buildVersion->setVersion($selectedMcVersion);
            $this->entityManager->persist($buildVersion);
        }

        foreach ($existingBuildVersions as $extraBuildVersion) {
            $this->entityManager->remove($extraBuildVersion);
        }
    }

    private function syncTags(Build $build, string $rawTags): void
    {
        $tagNames = array_values(array_filter(array_map(static fn($t) => trim($t), preg_split('/[\n,]+/', $rawTags) ?: [])));
        $tagNames = array_slice(array_values(array_unique($tagNames)), 0, 10);

        $desiredTagsByKey = [];
        foreach ($tagNames as $tagName) {
            if ($tagName === '') {
                continue;
            }

            $existingTag = $this->findOrCreateTag($tagName);
            $desiredTagsByKey[strtolower($existingTag->getName() ?? $tagName)] = $existingTag;
        }

        foreach ($build->getBuildTags()->toArray() as $existing) {
            $existingTagName = $existing->getTag()?->getName();
            $existingKey = $existingTagName ? strtolower($existingTagName) : null;
            if ($existingKey !== null && isset($desiredTagsByKey[$existingKey])) {
                unset($desiredTagsByKey[$existingKey]);
                continue;
            }

            $this->entityManager->remove($existing);
        }

        foreach ($desiredTagsByKey as $tag) {
            $this->entityManager->persist(new BuildTag($build, $tag));
        }
    }

    private function deleteWorldAssets(Build $build): void
    {
        $buildAssetsDir = rtrim($this->getBuildAssetsDirectory(), '/');
        foreach ($build->getAssets()->toArray() as $existingAsset) {
            if ($existingAsset->getType() !== BuildAssetType::WORLD) {
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
            $this->entityManager->remove($existingAsset);
        }
    }

    private function findOrCreateTag(string $tagName): Tag
    {
        $existing = $this->tagRepository->findOneBy(['name' => $tagName]);
        if ($existing) {
            return $existing;
        }

        $tag = new Tag();
        $tag->setName($tagName);

        $baseSlug = strtolower((string) $this->slugger->slug($tagName));
        $baseSlug = $baseSlug ?: strtolower(bin2hex(random_bytes(6)));
        $slug = $baseSlug;
        if ($this->tagRepository->findOneBy(['slug' => $slug])) {
            $slug = $baseSlug . '-' . strtolower(bin2hex(random_bytes(3)));
        }

        $tag->setSlug($slug);
        $this->entityManager->persist($tag);

        return $tag;
    }

    private function getBuildImagesDirectory(): string
    {
        return (string) $this->parameterBag->get('build_images_directory');
    }

    private function getBuildAssetsDirectory(): string
    {
        return (string) $this->parameterBag->get('build_assets_directory');
    }

}
