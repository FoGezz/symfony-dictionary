<?php

namespace App\Controller;

use App\Entity\Definition;
use App\Form\DefinitionType;
use App\Repository\DefinitionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/definition')]
class DefinitionController extends AbstractController
{
    #[Route('/', name: 'app_definition_index', methods: ['GET'])]
    public function index(DefinitionRepository $definitionRepository): Response
    {
        return $this->render('definition/index.html.twig', [
            'definitions' => $definitionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_definition_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DefinitionRepository $definitionRepository): Response
    {
        $definition = new Definition();
        $form = $this->createForm(DefinitionType::class, $definition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $definitionRepository->add($definition);
            return $this->redirectToRoute('app_definition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('definition/new.html.twig', [
            'definition' => $definition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_definition_show', methods: ['GET'])]
    public function show(Definition $definition): Response
    {
        return $this->render('definition/show.html.twig', [
            'definition' => $definition,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_definition_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Definition $definition, DefinitionRepository $definitionRepository): Response
    {
        $form = $this->createForm(DefinitionType::class, $definition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $definitionRepository->add($definition);
            return $this->redirectToRoute('app_definition_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('definition/edit.html.twig', [
            'definition' => $definition,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_definition_delete', methods: ['POST'])]
    public function delete(Request $request, Definition $definition, DefinitionRepository $definitionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$definition->getId(), $request->request->get('_token'))) {
            $definitionRepository->remove($definition);
        }

        return $this->redirectToRoute('app_definition_index', [], Response::HTTP_SEE_OTHER);
    }
}
