<?php

declare(strict_types=1);

namespace PayonePayment\Payone\RequestParameter\Builder\Capture;

use PayonePayment\Installer\CustomFieldInstaller;
use PayonePayment\Payone\RequestParameter\Builder\AbstractRequestParameterBuilder;
use PayonePayment\Payone\RequestParameter\Struct\FinancialTransactionStruct;
use Shopware\Core\Checkout\Payment\Exception\InvalidOrderException;
use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\System\Currency\CurrencyEntity;

class RefundRequestParameterBuilder extends AbstractRequestParameterBuilder
{
    /** @param FinancialTransactionStruct $arguments */
    public function getRequestParameter(
        Struct $arguments
    ): array {
        $totalAmount  = $arguments->getRequestData()->get('amount');
        $order        = $arguments->getPaymentTransaction()->getOrder();
        $customFields = $arguments->getPaymentTransaction()->getCustomFields();

        if ($totalAmount === null) {
            $totalAmount = $order->getAmountTotal();
        }

        if (empty($customFields[CustomFieldInstaller::TRANSACTION_ID])) {
            throw new InvalidOrderException($order->getId());
        }

        if ($customFields[CustomFieldInstaller::SEQUENCE_NUMBER] === null || $customFields[CustomFieldInstaller::SEQUENCE_NUMBER] === '') {
            throw new InvalidOrderException($order->getId());
        }

        /** @var CurrencyEntity $currency */
        $currency = $order->getCurrency();

        return [
            'request'        => 'debit',
            'txid'           => $customFields[CustomFieldInstaller::TRANSACTION_ID],
            'sequencenumber' => $customFields[CustomFieldInstaller::SEQUENCE_NUMBER] + 1,
            'amount'         => -1 * $this->getConvertedAmount((float) $totalAmount, $currency->getDecimalPrecision()),
            'currency'       => $currency->getIsoCode(),
        ];
    }

    public function supports(Struct $arguments): bool
    {
        if (!($arguments instanceof FinancialTransactionStruct)) {
            return false;
        }

        if ($arguments->getAction() === self::REQUEST_ACTION_REFUND) {
            return true;
        }

        return false;
    }
}
