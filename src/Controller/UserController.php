<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Form\UserPasswordForm;
use App\Form\UserType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserService $userService): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userService->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserService $userService): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->create($user);

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
    public function show(User $user, int $page = 1, UserService $userService, Request $request): Response
    {
        $page = max(1, $page);
        $section = (string) $request->attributes->get('_route');
        $actualUser = $this->getUser();

        if($user->isActive() === false){
            return $this->redirectToRoute('app_user_not_found', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/show.html.twig', $userService->getProfileData($user, $section, $page, $actualUser instanceof User ? $actualUser : null));
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserService $userService): Response
    {


        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userService->updateProfile($user, $form->get('avatar_url')->getData());

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit/password', name: 'app_user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(Request $request, User $user, UserService $userService): Response
    {

        if ($user !== $this->getUser()) {
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(UserPasswordForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('plainPassword')->getData();

            if (!$userService->updatePassword($user, $currentPassword, $newPassword)) {
                $form->get('currentPassword')->addError(new FormError('Mot de passe actuel incorrect.'));
            } else {
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
    public function editPrivacy(Request $request, User $user): Response
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
    public function delete(Request $request, User $user, UserService $userService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($user !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez supprimer que votre propre compte.');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $request->getSession()->invalidate();
            $userService->remove($user);
        }

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/follow/{id}', name: 'app_user_get_follow_list', methods: ['GET'], defaults: ['type' => 'followers'])]
    #[Route('/following/{id}', name: 'app_user_get_following_list', methods: ['GET'], defaults: ['type' => 'following'])]
    public function getFollowList(
        int $id,
        string $type,
        UserService $userService
    ): Response {
        try {
            $data = $userService->getFollowList($id, $type);
        } catch (UserNotFoundException $exception) {
            throw $this->createNotFoundException($exception->getMessage(), $exception);
        }

        return $this->render('user/followList.html.twig', $data);
    }
}
