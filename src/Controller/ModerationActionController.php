<?php

namespace App\Controller;

use App\Entity\ModerationAction;
use App\Entity\User;
use App\Exception\ModerationTargetNotFoundException;
use App\Form\ModerationActionType;
use App\Service\ModerationActionResult;
use App\Service\ModerationActionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/moderation/action')]
final class ModerationActionController extends AbstractController
{
    #[Route(name: 'app_moderation_action_index', methods: ['GET'])]
    public function index(ModerationActionService $moderationActionService): Response
    {
        return $this->render('moderation_action/index.html.twig', [
            'moderation_actions' => $moderationActionService->findAll(),
        ]);
    }

    #[Route('/{type}/{id}', name: 'app_moderation_action_delete', methods: ['POST'])]
    public function delete(string $type, Request $request, ModerationActionService $moderationActionService, int $id): Response
    {
        $result = null;

        if ($this->isCsrfTokenValid('delete' . $id, $request->getPayload()->getString('_token'))) {
            $moderator = $this->getUser();
            if (!$moderator instanceof User) {
                throw $this->createAccessDeniedException();
            }

            try {
                $result = $moderationActionService->deleteTarget(
                    $type,
                    $id,
                    $moderator,
                    $request->getPayload()->getString('reason'),
                    $request->getPayload()->getString('reason_code', 'other'),
                );
            } catch (ModerationTargetNotFoundException $exception) {
                throw $this->createNotFoundException($exception->getMessage(), $exception);
            }
        }

        $redirectTo = $request->request->get('_redirect_to');
        if (is_string($redirectTo) && str_starts_with($redirectTo, '/') && !str_starts_with($redirectTo, '//')) {
            return $this->redirect($redirectTo, Response::HTTP_SEE_OTHER);
        }

        if ($this->shouldRedirectToBuild($type, $result)) {
            return $this->redirectToRoute('app_build_show', [
                'id' => $result->redirectBuild->getId()
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
    public function edit(Request $request, ModerationAction $moderationAction, ModerationActionService $moderationActionService): Response
    {
        $form = $this->createForm(ModerationActionType::class, $moderationAction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $moderationActionService->save();

            return $this->redirectToRoute('app_moderation_action_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moderation_action/edit.html.twig', [
            'moderation_action' => $moderationAction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moderation_action_del', methods: ['POST'])]
    public function del(Request $request, ModerationAction $moderationAction, ModerationActionService $moderationActionService): Response
    {
        if ($this->isCsrfTokenValid('delete' . $moderationAction->getId(), $request->getPayload()->getString('_token'))) {
            $moderationActionService->remove($moderationAction);
        }

        return $this->redirectToRoute('app_moderation_action_index', [], Response::HTTP_SEE_OTHER);
    }

    private function shouldRedirectToBuild(string $type, ?ModerationActionResult $result): bool
    {
        return $type === 'comment' && $result?->redirectBuild !== null;
    }
}
