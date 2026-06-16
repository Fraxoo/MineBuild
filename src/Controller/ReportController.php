<?php

namespace App\Controller;

use App\Entity\ModerationAction;
use App\Entity\Report;
use App\Form\ReportType;
use App\Repository\BuildRepository;
use App\Repository\CommentRepository;
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
    #[Route('/{page}', name: 'app_report_index', defaults: ['page' => 1], methods: ['GET'])]
    public function index(ReportRepository $reportRepository, int $page): Response
    {
        $page = max(1, $page);


        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository->findAllWithIncludeAndPagination(10, $page),
        ]);
    }

    #[Route('/history', name: 'app_report_history', methods: ['GET'])]
    public function history(ReportRepository $reportRepository): Response
    {
        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository->findAll(),
        ]);
    }

    #[Route('/users', name: 'app_report_users', methods: ['GET'])]
    public function users(ReportRepository $reportRepository): Response
    {
        return $this->render('report/index.html.twig', [
            'reports' => $reportRepository->findAll(),
        ]);
    }


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
            $report->setCreatedAt(new \DateTimeImmutable());
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
                
            } elseif($report->getTargetType() === "build"){
                $build = $report->getBuild();
                $build->setVisibility("HIDDEN");
                $action->setBuild($build);
            } elseif($report->getTargetType() === "user"){
                $user = $report->getUser();
                $user->setIsActive(false);
            }


            $action->setCreatedAt(new DateTimeImmutable());
            $action->setTargetType($report->getTargetType());
            $action->setModerator($this->getUser());
            $action->setTargetUser($report->getUser());
            $action->setReason($request->request->get('reason'));

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
