<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

    #[Route('/quick-loan/careers', name: 'app_careers')]
    public function careers(): Response
    {
        return $this->render('home/careers.html.twig');
    }

    #[Route('/quick-loan/careers/notify', name: 'app_careers_notify')]
    public function careersNotify(): Response
    {
        return $this->render('home/careersNotify.html.twig');
    }
}
