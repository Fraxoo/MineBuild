<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NotFoundController extends AbstractController
{
    #[Route('/error/user/404', name: 'app_user_not_found')]
    public function userNotFound(): Response
    {
        return $this->render('not_found/user.html.twig', [
            'controller_name' => 'NotFoundController',
        ]);
    }

        #[Route('/error/build/404', name: 'app_build_not_found')]
    public function buildNotFound(): Response
    {
        return $this->render('not_found/build.html.twig', [
            'controller_name' => 'NotFoundController',
        ]);
    }
}
