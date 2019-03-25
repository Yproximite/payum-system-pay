<?php

declare(strict_types=1);

namespace Yproximite\Payum\SystemPay;

class PaymentConfigGenerator
{
    /**
     * @param string|array $input
     */
    public function generate($input): string
    {
        if (is_string($input)) {
            static $validPaymentConfigs = [Api::PAYMENT_CONFIG_SINGLE];
            $input                      = mb_strtoupper($input);

            if (!in_array($input, $validPaymentConfigs, true)) {
                throw new \InvalidArgumentException(sprintf(
                    'The given input "%s" is not a valid payment configuration, valid values are "%s".',
                    $input,
                    implode('", "', $validPaymentConfigs)
                ));
            }

            return $input;
        }

        if (is_array($input)) {
            if (isset($input['amount']) && isset($input['count']) && isset($input['period'])) {
                return sprintf(
                    'MULTI:first=%s;count=%s;period=%s',
                    $input['amount'] / $input['count'],
                    $input['count'],
                    $input['period']
                );
            }

            if ($this->isMultiByDateIsValid($input)) {
                return sprintf(
                    'MULTI_EXT:%s',
                    implode(';', array_map(function (array $period) {
                        ['date' => $date, 'amount' => $amount] = $period;

                        return sprintf('%s=%s', $date->format('Ymd'), $amount);
                    }, $input))
                );
            }

            throw new \InvalidArgumentException(
                'The given input array is not valid. It should be either an array with keys "amount", "count", and "period", or an array of arrays with keys "date" and "amount".'
            );
        }

        throw new \InvalidArgumentException(sprintf(
            'The given input "%s" should be a string or an array.',
            $input
        ));
    }

    protected function isMultiByDateIsValid(array $input)
    {
        $isValid = true;

        foreach ($input as $period) {
            $isValid = $isValid && isset($period['date']) && $period['date'] instanceof \DateTimeInterface && isset($period['amount']);
        }

        return $isValid;
    }
}
