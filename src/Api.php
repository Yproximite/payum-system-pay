<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;
    /**
     * @var MessageFactory
     */
    protected $messageFactory;
    /**
     * @var array
     */
    protected $options = [];

    /**
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options        = $options;
        $this->client         = $client;
        $this->messageFactory = $messageFactory;
    }

    protected function doRequest(string $method, array $fields): ResponseInterface
    {
        $headers = [];

        $request = $this->messageFactory->createRequest($method, $this->getApiEndpoint(), $headers, http_build_query($fields));

        $response = $this->client->send($request);

        if (false === ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300)) {
            throw HttpException::factory($request, $response);
        }

        return $response;
    }

    protected function getApiEndpoint(): string
    {
        return true === $this->options['sandbox'] ? 'http://sandbox.example.com' : 'http://example.com';
    }
}
