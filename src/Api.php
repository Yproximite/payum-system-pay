<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

use Http\Message\MessageFactory;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;

class Api
{
    public const V2 = 'V2';

    public const ACTION_MODE_INTERACTIVE = 'INTERACTIVE';

    public const FIELD_VADS_SITE_ID        = 'vads_site_id';
    public const FIELD_VADS_CTX_MODE       = 'vads_ctx_mode';
    public const FIELD_VADS_TRANS_ID       = 'vads_trans_id';
    public const FIELD_VADS_TRANS_DATE     = 'vads_trans_date';
    public const FIELD_VADS_AMOUNT         = 'vads_amount';
    public const FIELD_VADS_CURRENCY       = 'vads_currency';
    public const FIELD_VADS_ACTION_MODE    = 'vads_action_mode';
    public const FIELD_VADS_PAGE_ACTION    = 'vads_page_action';
    public const FIELD_VADS_PAYMENT_CONFIG = 'vads_payment_config';
    public const FIELD_VADS_VERSION        = 'vads_version';
    public const FIELD_VADS_URL_CHECK      = 'vads_url_check';
    public const FIELD_VADS_TRANS_STATUS   = 'vads_trans_status';

    public const CONTEXT_MODE_TEST       = 'TEST';
    public const CONTEXT_MODE_PRODUCTION = 'PRODUCTION';

    public const PAGE_ACTION_PAYMENT = 'PAYMENT';

    public const PAYMENT_CONFIG_SINGLE = 'SINGLE';
    public const PAYMENT_CONFIG_MULTI  = 'MULTI';

    public const STATUS_ABANDONED                         = 'ABANDONED';
    public const STATUS_AUTHORISED                        = 'AUTHORISED';
    public const STATUS_AUTHORISED_TO_VALIDATE            = 'AUTHORISED_TO_VALIDATE';
    public const STATUS_CANCELLED                         = 'CANCELLED';
    public const STATUS_CAPTURED                          = 'CAPTURED';
    public const STATUS_CAPTURE_FAILED                    = 'CAPTURE_FAILED';
    public const STATUS_EXPIRED                           = 'EXPIRED';
    public const STATUS_INITIAL                           = 'INITIAL';
    public const STATUS_NOT_CREATED                       = 'NOT_CREATED';
    public const STATUS_REFUSED                           = 'REFUSED';
    public const STATUS_SUSPENDED                         = 'SUSPENDED';
    public const STATUS_UNDER_VERIFICATION                = 'UNDER_VERIFICATION';
    public const STATUS_WAITING_AUTHORISATION             = 'WAITING_AUTHORISATION';
    public const STATUS_WAITING_AUTHORISATION_TO_VALIDATE = 'WAITING_AUTHORISATION_TO_VALIDATE';

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
        $details[self::FIELD_VADS_CTX_MODE]       = $this->getOption($details, self::FIELD_VADS_CTX_MODE) ?? $this->getContextMode();
        $details[self::FIELD_VADS_SITE_ID]        = $this->getOption($details, self::FIELD_VADS_SITE_ID);
        $details[self::FIELD_VADS_ACTION_MODE]    = $this->getOption($details, self::FIELD_VADS_ACTION_MODE);
        $details[self::FIELD_VADS_PAGE_ACTION]    = $this->getOption($details, self::FIELD_VADS_PAGE_ACTION);
        $details[self::FIELD_VADS_PAYMENT_CONFIG] = $this->getOption($details, self::FIELD_VADS_PAYMENT_CONFIG);
        $details[self::FIELD_VADS_VERSION]        = $this->getOption($details, self::FIELD_VADS_VERSION);

        $details['signature'] = $this->signatureGenerator->generate($details, $this->getCertificate());

        throw new HttpPostRedirect($this->getApiEndpoint(), $details);
    }

    protected function getApiEndpoint(): string
    {
        return 'https://paiement.systempay.fr/vads-payment/';
    }

    protected function getContextMode(): string
    {
        return true === $this->options['sandbox']
            ? self::CONTEXT_MODE_TEST
            : self::CONTEXT_MODE_PRODUCTION;
    }

    protected function getCertificate(): string
    {
        return self::CONTEXT_MODE_PRODUCTION === $this->getContextMode()
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
