<?php
/**
 * @file
 * Contains Wikipedia\Action\OpenSearch.
 */

namespace AlbertVolkman\Wikipedia\Action;


use Guzzle\Http\Client;

class OpenSearch {

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
    public $action = 'opensearch';

    /**
     * The number of results to return.
     *
     * @var integer
     */
    public $limit = 10;

    /**
     * Service that provides an HTTP client.
     *
     * @var Client
     */
    protected $client;

    /**
     * Creates an instance of the Parse class.
     *
     * @param Client $client
     *   Service that provides an HTTP client.
     */
    public function __construct(Client $client)
    {
        // Dependency injection.
        $this->client = $client;

        // Build URL.
        $this->url = 'http://' . $this->endpoint
            . '?format=' . $this->format
            . '&action=' . $this->action
            . '&limit=' . $this->limit
            . '&namespace=0'
        ;
    }

    public function search($query)
    {
        $query = $this->url
            . '&search=' . $query;

        return $this->send($query);
    }

    protected function send($query)
    {
        $response = $this->client->get($query)->send();

        return $response->json();
    }
}
