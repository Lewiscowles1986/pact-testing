<?php

declare(strict_types=1);

namespace Tests\Consumer;

use App\Consumer\PokemonClient;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServer;
use PhpPact\Standalone\MockService\MockServerConfig;
use PhpPact\Standalone\MockService\MockServerEnvConfig;
use PHPUnit\Framework\TestCase;

class GetAllTest extends TestCase
{
    public $server;

    public function setUp(): void
    {
        $config = new MockServerConfig();
        $config->setHost('localhost');
        $config->setPort(7200);
        $config->setConsumer('someConsumer');
        $config->setProvider('someProvider');
        $config->setCors(true);
        $config->setHealthCheckTimeout(10);
        $config->setHealthCheckRetrySec(1);

        $server = new MockServer($config);

        $this->server = $server;

        // Create the process.
        $server->start();
    }

    public function tearDown(): void
    {
        $this->server->stop();
    }

    /**
     * @test
     */
    public function getAllPokemon(): void
    {
        $matcher = new Matcher();

        // Create your expected request from the consumer.
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/pokemon');

        // Create your expected response from the provider.
        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->setBody(
                $matcher->eachLike( ['name' => 'pikachu', 'pokemon_number' => 4]));

        // Create a configuration that reflects the server that was started. You can create a custom MockServerConfigInterface if needed.
        $config  = new MockServerEnvConfig();
        $builder = new InteractionBuilder($config);
        $builder
            ->uponReceiving('A get request to pokemon')
            ->with($request)
            ->willRespondWith($response); // This has to be last. This is what makes an API request to the Mock Server to set the interaction.

        $service = new PokemonClient($config->getBaseUri()); // Pass in the URL to the Mock Server.
        $result  = $service->getAll(); // Make the real API request against the Mock Server.

        $builder->verify(); // This will verify that the interactions took place.

        $this->assertCount(1, $result);
    }
}
