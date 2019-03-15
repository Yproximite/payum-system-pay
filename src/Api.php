<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

use Http\Message\MessageFactory;
use Payum\Core\HttpClientInterface;
use Payum\Core\Reply\HttpPostRedirect;

class Api
{
    /**
     * Version #2 of the protocol exchange with the gateway, used in field `vads_version`
     */
    public const V2 = 'V2';

    /**
     * The payment is interactive, used in field `vads_action_mode`
     */
    public const ACTION_MODE_INTERACTIVE = 'INTERACTIVE';

    /**
     * Use the "test" mode of interaction with the payment gateway, used in field `vads_ctx_mode`
     */
    public const CONTEXT_MODE_TEST = 'TEST';

    /**
     * Use the "production" mode of interaction with the payment gateway, used in field `vads_ctx_mode`
     */
    public const CONTEXT_MODE_PRODUCTION = 'PRODUCTION';

    /**
     * Perform the "payment" action, used in field `vads_page_action`
     */
    public const PAGE_ACTION_PAYMENT = 'PAYMENT';

    /**
     * Configure the payment in "SINGLE" mode, used in field `vads_payment_config`
     */
    public const PAYMENT_CONFIG_SINGLE = 'SINGLE';

    /**
     * Generated while subscribing to the payment gateway. (input field, mandatory)
     * Its value can be seen in the interface of the Back Office in Settings > Shop > Certificates tab by all authorized persons.
     */
    public const FIELD_VADS_SITE_ID = 'vads_site_id';

    /**
     * Defines the mode of interaction with the payment gateway. (input field, mandatory)
     */
    public const FIELD_VADS_CTX_MODE = 'vads_ctx_mode';

    /**
     * 6 numeric characters and that should be unique for every transaction within a given shop over one day. (input field, mandatory)
     */
    public const FIELD_VADS_TRANS_ID = 'vads_trans_id';

    /**
     * Corresponds to the timestamp in the YYYYMMDDHHMMSS format. (input field, mandatory)
     */
    public const FIELD_VADS_TRANS_DATE = 'vads_trans_date';

    /**
     * Allows to define the transaction status. (output field)
     */
    public const FIELD_VADS_TRANS_STATUS = 'vads_trans_status';

    /**
     * The amount of the transaction presented in the smallest unit of the currency. (input field, mandatory)
     * For a transaction of 10 euros and 28 cents, the value is "1028".
     */
    public const FIELD_VADS_AMOUNT = 'vads_amount';

    /**
     * Numeric currency code to be used for the payment, in compliance with the ISO 4217 standard. (input field, mandatory)
     */
    public const FIELD_VADS_CURRENCY = 'vads_currency';

    /**
     * Data acquisition mode of the credit card details. (input field, mandatory)
     */
    public const FIELD_VADS_ACTION_MODE = 'vads_action_mode';

    /**
     * Defines the action to be performed. (input field, mandatory)
     */
    public const FIELD_VADS_PAGE_ACTION = 'vads_page_action';

    /**
     * Defines the type of payment: immediate or installment. (input field, mandatory)
     *
     *  - For a single payment, the value must be set to "SINGLE"
     *  - For an installment payment with fixed amounts and dates,
     *    the value must be set to "MULTI": followed by "key=value" pairs separated by the ";" character.
     *    The parameters are:
     *      - "first": indicates the amount of the first installment (populated in the smallest unit of the currency).
     *      - "count": indicates the total number of installments.
     *      - "period": indicates the number of days between 2 installments.
     *    The field order associated with MULTI must be respected.
     */
    public const FIELD_VADS_PAYMENT_CONFIG = 'vads_payment_config';

    /**
     * Version of the exchange protocol with the payment gateway. (input field, mandatory)
     */
    public const FIELD_VADS_VERSION = 'vads_version';

    /**
     * Default URL to where the buyer will be redirected after having clicked on Return to shop,
     * if `vads_url_error`, `vads_url_refused`, `vads_url_success` or `vads_url_cancel` is not set. (input field, optional)
     */
    public const FIELD_VADS_URL_RETURN = 'vads_url_return';

    /**
     * URL of the page to notify at the end of payment. Overrides the value entered in the notification rules settings. (input field, optional)
     */
    public const FIELD_VADS_URL_CHECK = 'vads_url_check';

    /**
     * Return code of the requested action. (output field)
     * Possible values:
     *   - 00: Action successfully completed.
     *   - 02: The merchant must contact the cardholder's bank Deprecated.
     *   - 05: Action rejected.
     *   - 17: Action canceled by the buyer.
     *   - 30: Request format error. To match with the value of the `vads_extra_result` field.
     *   - 96: Technical details.
     */
    public const FIELD_VADS_RESULT = 'vads_result';

    /**
     * Buyer ID (identification by the merchant). (input field, optional)
     */
    public const FIELD_VADS_CUSTOMER_ID = 'vads_cust_id';

    /**
     * Buyer's e-mail address, required if you want the payment gateway to send an e-mail to the buyer. (input field, optional)
     * In order to make sure the buyer receives an e-mail, make sure to post this parameter in the form when you generate a payment request.
     */
    public const FIELD_VADS_CUSTOMER_EMAIL = 'vads_cust_email';

    /**
     * Payment abandoned by the buyer.
     * The transaction has not been created, and therefore cannot be viewed in the Back Office.
     */
    public const STATUS_ABANDONED = 'ABANDONED';

    /**
     * The transaction has been accepted and will be automatically captured at the bank on the expected date.
     */
    public const STATUS_AUTHORISED = 'AUTHORISED';

    /**
     * The transaction, created with manual validation, is authorized. The merchant must manually validate the transaction in order for it to be captured.
     *
     * The transaction can be validated as long as the expiration date of the authorization request has not passed. If the authorization validity period has passed, the payment status changes to EXPIRED. The Expired status is final.
     */
    public const STATUS_AUTHORISED_TO_VALIDATE = 'AUTHORISED_TO_VALIDATE';

    /**
     * The transaction has been canceled by the merchant.
     */
    public const STATUS_CANCELLED = 'CANCELLED';

    /**
     * The transaction has been captured by the bank
     */
    public const STATUS_CAPTURED = 'CAPTURED';

    /**
     * The transaction capture has failed.
     * Contact the technical support.
     */
    public const STATUS_CAPTURE_FAILED = 'CAPTURE_FAILED';

    /**
     * The expiry date of the authorization request has passed and the merchant has not validated the transaction.
     * The account of the cardholder will therefore, not be debited.
     */
    public const STATUS_EXPIRED = 'EXPIRED';

    /**
     * This status concerns all the payment methods that require integration via a payment form with redirection.
     * This status is returned when:
     *   - no response has been returned by the acquirer
     *   - or when the delay of the response from the acquirer has exceeded the payment session on the payment gateway.
     *
     * This status is temporary.
     * The final status will be displayed in the Back Office immediately after the synchronization has been completed.
     */
    public const STATUS_INITIAL = 'INITIAL';

    /**
     * The transaction has not been created and cannot be viewed in the Back Office.
     */
    public const STATUS_NOT_CREATED = 'NOT_CREATED';

    /**
     * The transaction has been rejected.
     */
    public const STATUS_REFUSED = 'REFUSED';

    /**
     * The capture of the transaction is temporarily blocked by the acquirer (AMEX GLOBAL or SECURE TRADING).
     * Once the transaction has been correctly captured, its status changes to CAPTURED.
     */
    public const STATUS_SUSPENDED = 'SUSPENDED';

    /**
     * Under verification (Specific to PayPal).
     */
    public const STATUS_UNDER_VERIFICATION = 'UNDER_VERIFICATION';

    /**
     * The capture delay exceeds the authorization validity period.
     */
    public const STATUS_WAITING_AUTHORISATION = 'WAITING_AUTHORISATION';

    /**
     * The capture delay exceeds the authorization validity period.
     * An authorization of 1 euro (or request for information on the CB network if the buyer supports it) has been accepted.
     * The merchant must manually validate the transaction for the authorization request and the capture to occur.
     */
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
