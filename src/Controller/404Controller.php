<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class 404Controller extends AbstractController
{
    #[Route('/404', name: 'app_404')]
    public function index(): Response
    {
        return $this->render('404/index.html.twig', [
            'controller_name' => '404Controller',
        ]);
    }
}
