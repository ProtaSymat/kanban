<?php
namespace App\Controller;

use App\Entity\Board;
use App\Form\BoardType;
use App\Repository\BoardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/board')]
class BoardController extends AbstractController
{
    #[Route('/', name: 'app_board_index', methods: ['GET'])]
    public function index(BoardRepository $boardRepository): Response
    {
        return $this->render('board/index.html.twig', [
            'boards' => $boardRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_board_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un board.');
            return $this->redirectToRoute('app_login');
        }

        $board = new Board();
        $board->setCreatedAt(new \DateTime());

        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $board->setUser($this->getUser());
            $entityManager->persist($board);
            $entityManager->flush();

            return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('board/new.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_board_show', methods: ['GET'])]
    public function show(Board $board): Response
    {
        return $this->render('board/show.html.twig', [
            'board' => $board,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_board_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Board $board, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoardType::class, $board);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('board/edit.html.twig', [
            'board' => $board,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_board_delete', methods: ['POST'])]
    public function delete(Request $request, Board $board, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $board->getId(), $request->request->get('_token'))) {
            $entityManager->remove($board);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_board_index', [], Response::HTTP_SEE_OTHER);
    }
}
