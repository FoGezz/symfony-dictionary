<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:search-term',
    description: 'DEBUG: GET $hostname/rpc/searchTerm/{param}',
)]
class SearchTermCommand extends Command
{
    public function __construct(private HttpClientInterface $client, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->addArgument('term', InputArgument::REQUIRED, 'Term or substring of term to find in dictionary');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output, ): int
    {
        $io = new SymfonyStyle($input, $output);
        $term = $input->getArgument('term');

        $response = $this->client->request(
            method: 'GET',
            url: 'http://nginx:80/rpc/searchTerm/' . $term,
        );

        $io->text($response->getContent());


        return Command::SUCCESS;
    }
}
