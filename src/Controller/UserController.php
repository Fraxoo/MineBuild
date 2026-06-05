<?php

namespace App\Controller;

use App\Entity\Build;
use App\Entity\User;
use App\Form\UserPasswordForm;
use App\Form\UserType;
use App\Repository\BuildRepository;
use App\Repository\UserFollowRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormError;


#[Route('/user')]
final class UserController extends AbstractController
{

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {

    }


    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/all/{page}', name: 'app_user_show', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/{id}/favorites/{page}', name: 'app_user_favorites', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/{id}/following/{page}', name: 'app_user_following', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/{id}/followers/{page}', name: 'app_user_followers', defaults: ['page' => 1], methods: ['GET'])]
    public function show(User $user, int $page = 1, BuildRepository $buildRepository, Request $request): Response
    {

        $page = max(1, $page);
        $items = null;
        $limit = 12;
        $totalItems = 0;
        $section = $request->attributes->get('_route');

        if ($section === 'app_user_favorites') {
            $items = $items = $buildRepository->findPaginatedOnlineBuilds($page, 12, [], $user, true);
            $totalItems = $buildRepository->countOnlineBuildsWithFilters([], $user, true);
        } elseif ($section === 'app_user_following') {
            $items = $user->getFollowingRelations();
        } elseif ($section === 'app_user_followers') {
            $items = $user->getFollowerRelations();
        } else {
            $items = $buildRepository->findPaginatedOnlineBuilds($page, 12, [], $user);
            $totalItems = $buildRepository->countOnlineBuildsWithFilters([], $user);
        }

        $totalPages = ceil($totalItems / $limit);

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'totalLikes' => $buildRepository->getAllLikeForAllBuildsByUser($user),
            'totalViews' => $buildRepository->getTotalViewForAllBuildsByUser($user),
            'totalSaves' => $buildRepository->getTotalSaveForAllBuildsByUser($user),
            'items' => $items,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'currentPage' => $page
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {


        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('avatar_url')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }

                $user->setAvatarUrl($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit/password', name: 'app_user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, UserPasswordHasherInterface $passwordHasher, User $user, EntityManagerInterface $entityManager): Response
    {

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(UserPasswordForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();

            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $form->get('currentPassword')->addError(new FormError('Mot de passe actuel incorrect.'));
            } else {
                $newPassword = $form->get('plainPassword')->getData();
                $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
                $entityManager->flush();

                return $this->render('user/passwordEdit.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }
        }

        return $this->render('user/passwordEdit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit/privacy', name: 'app_user_edit_privacy', methods: ['GET', 'POST'])]
    public function editPrivacy(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {


        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        // $form = $this->createForm(UserPasswordForm::class, $user);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        // }

        return $this->render('user/privacyEdit.html.twig', [
            'user' => $user,
            // 'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/follow/{id}', name: 'app_user_get_follow_list', methods: ['GET'], defaults: ['type' => 'followers'])]
    #[Route('/following/{id}', name: 'app_user_get_following_list', methods: ['GET'], defaults: ['type' => 'following'])]
    public function getFollowList(
        int $id,
        string $type,
        UserRepository $userRepository,
        UserFollowRepository $userFollowRepository
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        if ($type === 'followers') {
            // Ceux qui suivent cet utilisateur
            $follows = $userFollowRepository->findBy([
                'following' => $user,
            ]);
        } else {
            // Ceux que cet utilisateur suit
            $follows = $userFollowRepository->findBy([
                'follower' => $user,
            ]);
        }

        return $this->render('user/followList.html.twig', [
            'user' => $user,
            'follows' => $follows,
            'type' => $type,
        ]);
    }
}
