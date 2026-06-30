<?php

namespace App\Controller;

use App\Entity\ModerationAction;
use App\Entity\User;
use App\Enum\ModerationActionType as ModerationActionTypeEnum;
use App\Enum\ReportReasonCode;
use App\Enum\TargetType;
use App\Enum\Visibility;
use App\Form\ModerationActionType;
use App\Repository\BuildRepository;
use App\Repository\CommentRepository;
use App\Repository\ModerationActionRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/moderation/action')]
final class ModerationActionController extends AbstractController
{
    #[Route(name: 'app_moderation_action_index', methods: ['GET'])]
    public function index(ModerationActionRepository $moderationActionRepository): Response
    {
        return $this->render('moderation_action/index.html.twig', [
            'moderation_actions' => $moderationActionRepository->findAll(),
        ]);
    }

    #[Route('/{type}/{id}', name: 'app_moderation_action_delete', methods: ['POST'])]
    public function delete(string $type, Request $request, UserRepository $userRepo, BuildRepository $buildRepo, CommentRepository $commentRepo, EntityManagerInterface $entityManager, int $id): Response
    {
        if ($this->isCsrfTokenValid('delete' . $id, $request->getPayload()->getString('_token'))) {

            $action = new ModerationAction();
            $action->setAction(ModerationActionTypeEnum::DELETE);
            $user = null;
            $build = null;
            $targetType = TargetType::tryFrom($type);
            if (!$targetType) {
                throw $this->createNotFoundException('Target not found');
            }

            if ($type === "comment") {
                $comment = $commentRepo->find($id);
                if (!$comment) {
                    throw $this->createNotFoundException('Comment not found');
                }

                $action->setComment($comment);
                $comment->setVisibility(Visibility::HIDDEN);
                $user = $comment->getAuthor();
                $build = $comment->getBuild();
            } elseif ($type === "build") {
                $build = $buildRepo->find($id);
                if (!$build) {
                    throw $this->createNotFoundException('Build not found');
                }

                $build->setVisibility(Visibility::HIDDEN);
                $action->setBuild($build);
                $user = $build->getAuthor();
            } elseif ($type === "user") {
                $user = $userRepo->find($id);
                if (!$user) {
                    throw $this->createNotFoundException('User not found');
                }

                $user->setIsActive(false);
            }


            $action->setCreatedAt(new DateTimeImmutable());
            $action->setTargetType($targetType);
            $moderator = $this->getUser();
            if (!$moderator instanceof User) {
                throw $this->createAccessDeniedException();
            }

            $action->setModerator($moderator);
            $action->setTargetUser($user);
            $action->setReason($request->getPayload()->getString('reason'));
            $reasonCode = trim($request->getPayload()->getString('reason_code', 'other'));
            $action->setReasonCode(ReportReasonCode::tryFrom($reasonCode) ?? ReportReasonCode::OTHER);

            $entityManager->persist($action);
            $entityManager->flush();
        }

        $redirectTo = $request->request->get('_redirect_to');
        if (is_string($redirectTo) && str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, '//')) {
            return $this->redirect($redirectTo, Response::HTTP_SEE_OTHER);
        }

        if ($type === "comment" && $build) {
            return $this->redirectToRoute('app_build_show', [
                'id' => $build->getId()
            ], Response::HTTP_SEE_OTHER);

        } elseif ($type === "build") {
            return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);

        }
        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_moderation_action_show', methods: ['GET'])]
    public function show(ModerationAction $moderationAction): Response
    {
        return $this->render('moderation_action/show.html.twig', [
            'moderation_action' => $moderationAction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_moderation_action_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ModerationAction $moderationAction, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModerationActionType::class, $moderationAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_moderation_action_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moderation_action/edit.html.twig', [
            'moderation_action' => $moderationAction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moderation_action_del', methods: ['POST'])]
    public function del(Request $request, ModerationAction $moderationAction, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $moderationAction->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($moderationAction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_moderation_action_index', [], Response::HTTP_SEE_OTHER);
    }
}
