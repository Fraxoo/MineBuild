<?php

namespace App\Controller;

use App\Entity\ModerationAction;
use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\BuildRepository;
use App\Repository\CommentRepository;
use App\Repository\ModerationActionRepository;
use App\Repository\ReportRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard')]
final class ReportController extends AbstractController
{
#[Route('/{page<\d+>}', name: 'app_report_index', defaults: ['page' => 1 , 'targetType' => 'dashboard'], methods: ['GET'])]
#[Route('/users/{page<\d+>}', name: 'app_report_users', defaults: ['page' => 1 , 'targetType' => 'users'], methods: ['GET'])]
#[Route('/history/{page<\d+>}', name: 'app_report_history', defaults: ['page' => 1, 'targetType' => 'history'], methods: ['GET'])]
    public function index(string $targetType,ModerationActionRepository $maRepository, ReportRepository $reportRepository,Request $request, int $page): Response
    {
        $page = max(1, $page);
        $limit = 10;
        $totalItems = $reportRepository->countPendingReport();
        $items = 0;

        if($targetType === 'users'){
            $totalItems = $reportRepository->countReportByUser($this->getUser());
        }elseif ($targetType === 'history') {
            $totalItems = $maRepository->countHistoryReport();
            $items = $maRepository->findAllWithIncludeAndPagination($limit, $page);
        } else {
            $totalItems = $reportRepository->countPendingReport();
            $items = $reportRepository->findPendingWithIncludeAndPagination($limit, $page);
        }

        return $this->render('report/index.html.twig', [
            'items' => $items,
            'totalItems' => $totalItems,
            'currentPage' => $page,
            'totalPages' => ceil($totalItems / $limit),
        ]);
    }

    // public function history(ReportRepository $reportRepository, int $page): Response
    // {
    //     $page = max(1, $page);
    //     $limit = 10;
    //     $totalItems = $reportRepository->countPendingReport();



    //     return $this->render('report/index.html.twig', [
    //         'reports' => $reportRepository->findAllWithIncludeAndPagination($limit, $page),
    //         'totalItems' => $totalItems,
    //         'currentPage' => $page,
    //         'totalPages' => ceil($totalItems / $limit),
    //     ]);
    // }

    // public function users(ReportRepository $reportRepository, int $page): Response
    // {
    //     $page = max(1, $page);
    //     $limit = 10;
    //     $totalItems = $reportRepository->countPendingReport();


    //     return $this->render('report/index.html.twig', [
    //         'reports' => $reportRepository->findAllWithIncludeAndPagination($limit, $page),
    //         'totalItems' => $totalItems,
    //         'currentPage' => $page,
    //         'totalPages' => ceil($totalItems / $limit),
    //     ]);
    // }


    #[Route('/new/comment/{id}', name: 'app_report_new_comment', defaults: ['targetType' => 'comment'], methods: ['GET', 'POST'])]
    #[Route('/new/build/{id}', name: 'app_report_new_build', defaults: ['targetType' => 'build'], methods: ['GET', 'POST'])]
    #[Route('/new/user/{id}', name: 'app_report_new_user', defaults: ['targetType' => 'user'], methods: ['GET', 'POST'])]
    public function new(
        int $id,
        string $targetType,
        Request $request,
        UserRepository $userRepo,
        CommentRepository $commentRepo,
        BuildRepository $buildRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $report = new Report();
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $report->setCreatedAt(new DateTimeImmutable());
            $report->setReporter($this->getUser());
            $report->setStatus('Pending');
            $report->setTargetType($targetType);

            $target = match ($targetType) {
                'comment' => $commentRepo->find($id),
                'build' => $buildRepo->find($id),
                'user' => $userRepo->find($id),
                default => null,
            };

            if (!$target) {
                throw $this->createNotFoundException('Target not found');
            }

            match ($targetType) {
                'comment' => $report->setComment($target)->setUser($target->getAuthor()),
                'build' => $report->setBuild($target)->setUser($target->getAuthor()),
                'user' => $report->setUser($target),
            };

            $entityManager->persist($report);
            $entityManager->flush();

            if ($targetType === "build") {
                return $this->redirectToRoute('app_build_show', [
                    'id' => $id
                ], Response::HTTP_SEE_OTHER);
            } elseif ($targetType === "comment") {
                return $this->redirectToRoute('app_build_show', [
                    'id' => $commentRepo->find($id)->getBuild()->getId()
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
    public function edit(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReportType::class, $report);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('report/edit.html.twig', [
            'report' => $report,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_report_delete', methods: ['POST'])]
    public function delete(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $report->getId(), $request->getPayload()->getString('_token'))) {
            $report->setHandledAt(new DateTimeImmutable());
            $report->setHandledBy($this->getUser());
            $report->setStatus("Confirmed");
            $action = new ModerationAction();
            $action->setAction("Delete");

            if ($report->getTargetType() === "comment") {
                $comment = $report->getComment();
                $action->setComment($comment);
                $comment->setVisibility("HIDDEN");
            } elseif ($report->getTargetType() === "build") {
                $build = $report->getBuild();
                $build->setVisibility("HIDDEN");
                $action->setBuild($build);
            } elseif ($report->getTargetType() === "user") {
                $user = $report->getUser();
                $user->setIsActive(false);
            }


            $action->setCreatedAt(new DateTimeImmutable());
            $action->setTargetType($report->getTargetType());
            $action->setModerator($this->getUser());
            $action->setTargetUser($report->getUser());
            $action->setReason($request->request->get('reason'));
            $action->setReasonCode($report->getReasonCode() ?? 'other');
            $action->setReport($report);

            $entityManager->persist($action);
            $entityManager->persist($report);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/reject/{id}', name: 'app_report_reject', methods: ['POST'])]
    public function reject(Request $request, Report $report, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('reject' . $report->getId(), $request->getPayload()->getString('_token'))) {
            $report->setHandledAt(new DateTimeImmutable());
            $report->setHandledBy($this->getUser());
            $report->setStatus("Rejected");

            $entityManager->persist($report);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_report_index', [], Response::HTTP_SEE_OTHER);
    }

}
