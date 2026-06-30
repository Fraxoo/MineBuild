<?php

namespace App\Controller;

use App\Entity\Report;
use App\Entity\User;
use App\Exception\ReportTargetNotFoundException;
use App\Form\ReportType;
use App\Repository\UserRepository;
use App\Service\ReportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class ReportController extends AbstractController
{
    #[Route('/{page<\d+>}', name: 'app_report_index', defaults: ['page' => 1, 'targetType' => 'dashboard'], methods: ['GET'])]
    #[Route('/users/{page<\d+>}', name: 'app_report_users', defaults: ['page' => 1, 'targetType' => 'users'], methods: ['GET'])]
    #[Route('/history/{page<\d+>}', name: 'app_report_history', defaults: ['page' => 1, 'targetType' => 'history'], methods: ['GET'])]
    public function index(string $targetType, ReportService $reportService, int $page): Response
    {
        $page = max(1, $page);
        $limit = 10;

        return $this->render('report/index.html.twig', $reportService->getDashboardData($targetType, $page, $limit));
    }

    #[Route('/users/show/{id<\d+>}/{page<\d+>}', name: 'app_report_users_builds', defaults: ['page' => 1, 'targetType' => 'builds'], methods: ['GET'])]
    #[Route('/users/show/comments/{id<\d+>}/{page<\d+>}', name: 'app_report_users_comments', defaults: ['page' => 1, 'targetType' => 'comments'], methods: ['GET'])]
    #[Route('/users/show/reports/{id<\d+>}/{page<\d+>}', name: 'app_report_users_reports', defaults: ['page' => 1, 'targetType' => 'reports'], methods: ['GET'])]
    public function showUser(string $targetType, UserRepository $userRepository, ReportService $reportService, int $page, int $id): Response
    {
        $page = max(1, $page);
        $limit = 10;
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('report/index.html.twig', $reportService->getUserDashboardData($targetType, $user, $page, $limit));
    }

    #[Route('/new/comment/{id}', name: 'app_report_new_comment', defaults: ['targetType' => 'comment'], methods: ['GET', 'POST'])]
    #[Route('/new/build/{id}', name: 'app_report_new_build', defaults: ['targetType' => 'build'], methods: ['GET', 'POST'])]
    #[Route('/new/user/{id}', name: 'app_report_new_user', defaults: ['targetType' => 'user'], methods: ['GET', 'POST'])]
    public function new(
        int $id,
        string $targetType,
        Request $request,
        ReportService $reportService,
    ): Response {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reporter = $this->getUser();
            if (!$reporter instanceof User) {
                throw $this->createAccessDeniedException();
            }

            try {
                $reportService->createReport($report, $id, $targetType, $reporter);
            } catch (ReportTargetNotFoundException $exception) {
                throw $this->createNotFoundException($exception->getMessage(), $exception);
            }

            if ($targetType === 'build') {
                return $this->redirectToRoute('app_build_show', [
                    'id' => $id,
                ], Response::HTTP_SEE_OTHER);
            } elseif ($targetType === 'comment') {
                return $this->redirectToRoute('app_build_show', [
                    'id' => $report->getComment()->getBuild()->getId(),
                ], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('report/new.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_report_show', methods: ['GET'])]
    public function show(Report $report): Response
    {
        return $this->render('report/show.html.twig', [
            'report' => $report,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_report_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Report $report, ReportService $reportService): Response
    {
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reportService->save();

            return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('report/edit.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_report_delete', methods: ['POST'])]
    public function delete(Request $request, Report $report, ReportService $reportService): Response
    {
        if ($this->isCsrfTokenValid('delete' . $report->getId(), $request->getPayload()->getString('_token'))) {
            $moderator = $this->getUser();
            if (!$moderator instanceof User) {
                throw $this->createAccessDeniedException();
            }

            $reportService->confirm($report, $moderator, $request->getPayload()->getString('reason'));
        }

        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reject/{id}', name: 'app_report_reject', methods: ['POST'])]
    public function reject(Request $request, Report $report, ReportService $reportService): Response
    {
        if ($this->isCsrfTokenValid('reject' . $report->getId(), $request->getPayload()->getString('_token'))) {
            $moderator = $this->getUser();
            if (!$moderator instanceof User) {
                throw $this->createAccessDeniedException();
            }

            $reportService->reject($report, $moderator);
        }

        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }
}
