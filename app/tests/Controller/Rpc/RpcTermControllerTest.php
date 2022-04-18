<?php

namespace App\Tests\Controller\Rpc;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Term;
use App\Repository\TermRepository;
use Symfony\Component\Routing\RouterInterface;

class RpcTermControllerTest extends ApiTestCase
{
    /**
     * @covers \App\Controller\Rpc\RpcTermController::search
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testSearch(): void
    {
        $client = static::createClient();
        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');
        $router->getContext()->setHttpPort(8081); //должно быть в энвах
        $url = $router->generate('app_rpc_searchTerm', ['fragment' => 'test'], $router::ABSOLUTE_URL);
        $response = $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
        $arrayResponse = $response->toArray();
        $this->assertCount(1, $arrayResponse);
        $this->assertCount(2, $arrayResponse[0]['definitions']);
        foreach ($arrayResponse[0]['definitions'] as $definition) {
            $this->assertStringStartsWith('test_fixture_term1_def', $definition);
        }

        $url = $router->generate('app_rpc_searchTerm', ['fragment' => 'nothing'], $router::ABSOLUTE_URL);
        $response = $client->request('GET', $url);
        $this->assertEmpty($response->toArray());
    }

    /**
     * @covers \App\Controller\Rpc\RpcTermController::add
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testAdd(): void
    {
        $client = static::createClient();
        /** @var RouterInterface $router */
        $router = $client->getContainer()->get('router');
        $router->getContext()->setHttpPort(8081); //должно быть в энвах
        $url = $router->generate('app_rpc_addTerms', [],  $router::ABSOLUTE_URL);


        $response = $client->request('POST', $url, [
            'json' => ['term1_test_add' => ['definition1_test_add', 'definition2_test_add']],
        ]);
        $this->assertResponseIsSuccessful();


        /** @var TermRepository $termRepository */
        $termRepository = $client->getContainer()->get(TermRepository::class);
        $entities = $termRepository->findBy(['value' => 'term1_test_add']);
        $this->assertCount(1, $entities);
        $this->assertCount(2, $entities[0]->getDefinitions());
        $response = $client->request('POST', $url, [
            'json' => ['term1_test_add' => ['definition1_test_add', 'definition2_test_add']],
        ]);
        $this->assertResponseStatusCodeSame(418);


        $response = $client->request('POST', $url, [
            'json' => ['term1_test_add', '123', 555]
        ]);
        $this->assertResponseStatusCodeSame(400);
    }
}
