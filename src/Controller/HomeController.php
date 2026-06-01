<?php

namespace App\Controller;

use App\Repository\BuildRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    private BuildRepository $buildRepository;

    public function __construct(BuildRepository $buildRepository)
    {
        $this->buildRepository = $buildRepository;
    }



    #[Route('/home/{page}', name: 'app_home', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/home/{page}/{sortBy}', name: 'app_home', defaults: ['page' => 1], methods: ['GET'])]
    #[Route('/home/{page}/{filter}', name: 'app_home', defaults: ['page' => 1], methods: ['GET'])]
    public function index(Request $request, int $page, string $sortBy, string $filter): Response
    {
        $page = max(1, $page);
        $limit = 12;

        $items = $this->buildRepository->findPaginatedOnlineBuilds($page, $limit);
        $totalItems = $this->buildRepository->countOnlineBuilds();

        $totalPages = ceil($totalItems / $limit);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'items' => $items,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page,
        ]);
    }
}
