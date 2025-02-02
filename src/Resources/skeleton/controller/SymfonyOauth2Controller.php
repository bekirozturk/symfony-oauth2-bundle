<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SymfonyOauth2Controller extends AbstractController
{
    #[Route('/oauth2/success', name: 'app_oauth2_success')]
    public function success(): Response
    {
        $userInfo = $this->get('session')->get('oauth2_user_info');
        dd($userInfo);
    }
} 