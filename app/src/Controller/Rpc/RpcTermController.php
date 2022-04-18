<?php

namespace App\Controller\Rpc;

use App\Entity\Definition;
use App\Entity\Term;
use App\Repository\DefinitionRepository;
use App\Repository\TermRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RpcTermController extends AbstractController
{
    public function __construct(
        private readonly TermRepository $termRepository,
    )
    {}

    #[Route('/rpc/searchTerm/{fragment}', name: 'app_rpc_searchTerm', methods: ['GET'])]
    public function search(string $fragment): Response
    {
        $alias = TermRepository::ALIAS;
        $defAlias = DefinitionRepository::ALIAS;

        /** @var array<Term> $terms */
        $terms = $this->termRepository->createQueryBuilder($alias)
            ->select($alias, $defAlias)
            ->where("LOWER($alias.value) LIKE LOWER(:term)")
            ->setParameter('term', "%$fragment%")
            ->leftJoin("$alias.definitions", $defAlias)
            ->getQuery()
            ->getResult();

        $arrayResponse = [];

        foreach ($terms as $term) {
            $definitions = $term->getDefinitions()->toArray();
            $arrayResponse[] = [
              'term' => $term->getValue(),
              'definitions' => array_map(fn(Definition $def): string => (string)$def->getValue(), $definitions),
            ];
        }

        return new JsonResponse($arrayResponse);
    }

    #[Route('/rpc/addTerms', name: 'app_rpc_addTerms', methods: ['POST'])]
    public function add(Request $request, DefinitionRepository $definitionRepository): Response
    {
        $payload = $request->toArray();

        foreach ($payload as $term => $definitions) {
            if (!is_array($definitions)) {
                return new Response(status:400);
            }

            $existingTerms = $this->termRepository->findBy(['value' => $term]);
            if ($existingTerms !== []) {
                return new Response(status: 418);
            }

            $eTerm = new Term();
            $eTerm->setValue($term);
            foreach ($definitions as $definition) {
                $eDefinition = new Definition();
                $eDefinition->setValue($definition);
                $definitionRepository->add($eDefinition);
                $eTerm->getDefinitions()->add($eDefinition);
            }

            $this->termRepository->add($eTerm);
        }

        return new Response();
    }

}
