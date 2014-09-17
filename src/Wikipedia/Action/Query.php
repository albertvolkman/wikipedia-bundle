<?php
/**
 * @file
 * Contains Wikipedia\Action\Query.
 */

namespace AlbertVolkman\Wikipedia\Action;

use AlbertVolkman\Wikipedia\Helper\Parser;
use Guzzle\Http\Client;

class Query
{
    /**
     * The path to Wikipedia's API endpoint.
     *
     * @var string
     */
    public $endpoint = 'en.wikipedia.org/w/api.php';

    /**
     * The requested format.
     *
     * @var string
     */
    public $format = 'json';

    /**
     * The requested action.
     *
     * @var string
     */
    public $action = 'query';

    /**
     * Service that provides an HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Service that provides access to the parser helper.
     *
     * @var Parse
     */
    protected $parser;

    /**
     * Creates an instance of the Parse class.
     *
     * @param Client $client
     *   Service that provides an HTTP client.
     * @param Parser $parser
     * Service that provides access to the parser helper.
     */
    public function __construct(Client $client, Parser $parser)
    {
        // Dependency injection.
        $this->client = $client;
        $this->parser = $parser;

        // Build URL.
        $this->url = 'http://' . $this->endpoint
            . '?format=' . $this->format
            . '&action=' . $this->action;
    }

    public function infoBox($titles)
    {
        $query = $this->url
            . '&rvsection=0'
            . '&prop=revisions'
            . '&rvprop=content'
            . '&titles=' . $this->parser->convertTitles($titles);

        return $this->send($query);
    }

    public function pageIds($titles)
    {
        $query = $this->url
            . '&indexpageids'
            . '&titles=' . $this->parser->convertTitles($titles);

        return $this->send($query);
    }

    protected function send($query)
    {
        $response = $this->client->get($query)->send();

        return $response->json();
    }
}
