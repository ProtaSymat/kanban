<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\BoardRepository;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'account_page')]
    public function index(BoardRepository $boardRepository): Response
    {
        $user = $this->getUser();
        $boards = $boardRepository->findBy(['user' => $user]);

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'boards' => $boards,
        ]);
    }
}
