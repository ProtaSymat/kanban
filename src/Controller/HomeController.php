<?php

namespace App\Controller;

use App\Repository\BoardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(BoardRepository $boardRepository): Response
    {
        $boards = $boardRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'boards' => $boards,
        ]);
    }
}