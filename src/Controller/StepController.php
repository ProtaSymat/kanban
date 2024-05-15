<?php
namespace App\Controller;

use App\Entity\Step;
use App\Entity\Board;
use App\Form\StepType;
use App\Repository\StepRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/step')]
class StepController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_step_index', methods: ['GET'])]
    public function index(StepRepository $stepRepository): Response
    {
        return $this->render('step/index.html.twig', [
            'steps' => $stepRepository->findAll(),
        ]);
    }
    #[Route('/new/{boardId}', name: 'app_step_new', methods: ['GET', 'POST'])]
    public function new(Request $request, int $boardId): Response
    {
        $board = $this->entityManager->getRepository(Board::class)->find($boardId);
    
        if (!$board) {
            throw $this->createNotFoundException('Le board avec l\'ID ' . $boardId . ' n\'existe pas.');
        }
    
        $step = new Step();
        $form = $this->createForm(StepType::class, $step);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $step->setBoard($board);
    
            $this->entityManager->persist($step);
            $this->entityManager->flush();
    
            return $this->redirectToRoute('app_board_show', ['id' => $boardId], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('step/new.html.twig', [
            'boardId' => $boardId,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_step_show', methods: ['GET'])]
    public function show(Step $step): Response
    {
        return $this->render('step/show.html.twig', [
            'step' => $step,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_step_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Step $step): Response
    {
        $form = $this->createForm(StepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            return $this->redirectToRoute('app_step_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('step/edit.html.twig', [
            'step' => $step,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_step_delete', methods: ['POST'])]
    public function delete(Request $request, Step $step): Response
    {
        if ($this->isCsrfTokenValid('delete'.$step->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($step);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_step_index', [], Response::HTTP_SEE_OTHER);
    }
}