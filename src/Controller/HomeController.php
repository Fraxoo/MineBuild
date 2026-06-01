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



    #[Route('/home', name: 'app_home' , methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
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
