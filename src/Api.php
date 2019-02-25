<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;
use Psr\Http\Message\ResponseInterface;
use Yproximite\Payum\SystemPay\Enum\ContextMode;
use Yproximite\Payum\SystemPay\Enum\RequestParam;

class Api
{
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var SignatureGenerator
     */
    private $signatureGenerator;
    /**
     * @var HttpClientInterface
     */
    protected $client;
    /**
     * @var MessageFactory
     */
    protected $messageFactory;

    /**
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     */
    public function __construct(array $options, SignatureGenerator $signatureGenerator, HttpClientInterface $client, MessageFactory $messageFactory)
    {
        $this->options            = $options;
        $this->signatureGenerator = $signatureGenerator;
        $this->client             = $client;
        $this->messageFactory     = $messageFactory;
    }

    public function doPayment(array $details): void
    {
        $details[RequestParam::VADS_CTX_MODE]       = $this->getOption($details, RequestParam::VADS_CTX_MODE) ?? $this->getContextMode();
        $details[RequestParam::VADS_SITE_ID]        = $this->getOption($details, RequestParam::VADS_SITE_ID);
        $details[RequestParam::VADS_ACTION_MODE]    = $this->getOption($details, RequestParam::VADS_ACTION_MODE);
        $details[RequestParam::VADS_PAGE_ACTION]    = $this->getOption($details, RequestParam::VADS_PAGE_ACTION);
        $details[RequestParam::VADS_PAYMENT_CONFIG] = $this->getOption($details, RequestParam::VADS_PAYMENT_CONFIG);
        $details[RequestParam::VADS_VERSION]        = $this->getOption($details, RequestParam::VADS_VERSION);

        $details['signature'] = $this->signatureGenerator->generate($details, $this->getCertificate());

        throw new HttpPostRedirect($this->getApiEndpoint(), $details);
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
        return 'https://paiement.systempay.fr/vads-payment/';
    }

    protected function getContextMode(): string
    {
        return true === $this->options['sandbox']
            ? ContextMode::TEST
            : ContextMode::PRODUCTION;
    }

    protected function getCertificate(): string
    {
        return ContextMode::PRODUCTION === $this->getContextMode()
            ? $this->options['certif_prod']
            : $this->options['certif_test'];
    }

    /**
     * @return mixed
     */
    protected function getOption(array $details, string $name)
    {
        if (array_key_exists($name, $details)) {
            return $details[$name];
        }

        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return null;
    }
}
