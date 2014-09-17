<?php
/**
 * @file
 * Contains Wikipedia\Action\Parse.
 */

namespace AlbertVolkman\Wikipedia\Action;

use Guzzle\Service\Client;

class Parse
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
    public $action = 'parse';

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
            . '&action=' . $this->action;
    }

    public function text($text)
    {
        $query = $this->url
            . '&text=' . urlencode($text);

        $response = $this->client->get($query)->send();

        return $response->json();
    }
}
