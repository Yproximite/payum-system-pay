<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

use Http\Message\MessageFactory;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;
use Psr\Http\Message\ResponseInterface;
use Yproximite\Payum\SystemPay\Enum\ContextMode;

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

    public function doPayment(array $details)
    {
        $fields = [
            'vads_site_id'        => $this->options['vads_site_id'],
            'vads_ctx_mode'       => $this->options['vads_ctx_mode'],
            'vads_trans_id'       => $details['vads_trans_id'],
            'vads_trans_date'     => $details['vads_trans_date'],
            'vads_amount'         => $details['vads_amount'],
            'vads_currency'       => $details['vads_currency'],
            'vads_action_mode'    => $this->options['vads_action_mode'],
            'vads_page_action'    => $this->options['vads_page_action'],
            'vads_payment_config' => $this->options['vads_payment_config'],
            'vads_version'        => $this->options['vads_version'],
        ];

        $fields['signature'] = $this->signatureGenerator->generate($fields, $this->getCertificate());

        throw new HttpPostRedirect($this->getApiEndpoint(), $fields);
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

    protected function getContextMode()
    {
        return true === $this->options['sandbox'] ? ContextMode::TEST : ContextMode::PRODUCTION;
    }

    protected function getCertificate(): string
    {
        return ContextMode::PRODUCTION === $this->getContextMode()
            ? $this->options['certif_prod']
            : $this->options['certif_test'];
    }
}
