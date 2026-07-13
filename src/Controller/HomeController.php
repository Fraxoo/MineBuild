<?php

namespace App\Controller;

use App\Service\HomeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/{page<\d+>}', name: 'app_home', defaults: ['page' => 1], methods: ['GET'])]
    public function index(Request $request, HomeService $homeService, int $page): Response
    {
        $page = max(1, $page);
        $limit = 12;

        return $this->render('home/index.html.twig', $homeService->getHomeData($request, $page, $limit));
    }
}
