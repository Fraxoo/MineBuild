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
    public function index(Request $request, int $page): Response
    {
        $page = max(1, $page);
        $limit = 12;

        $filters = [
            'search' => trim($request->query->get('search', '')),
            'category' => $request->query->getInt('category') ?: null,
            'difficulty' => $request->query->get('difficulty') ?: null,
            'sort' => $request->query->get('sort', 'DESC'),
        ];

        $items = $this->buildRepository->findPaginatedOnlineBuilds($page, $limit, $filters);
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
