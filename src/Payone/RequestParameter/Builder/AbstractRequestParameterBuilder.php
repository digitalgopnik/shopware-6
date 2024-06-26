<?php

declare(strict_types=1);

namespace PayonePayment\Payone\RequestParameter\Builder;

use PayonePayment\Installer\CustomFieldInstaller;
use PayonePayment\Payone\RequestParameter\Struct\AbstractRequestParameterStruct;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Currency\CurrencyEntity;

abstract class AbstractRequestParameterBuilder
{
    final public const REQUEST_ACTION_AUTHORIZE = 'authorization';
    final public const REQUEST_ACTION_PREAUTHORIZE = 'preauthorization';
    final public const REQUEST_ACTION_CAPTURE = 'capture';
    final public const REQUEST_ACTION_REFUND = 'refund';
    final public const REQUEST_ACTION_TEST = 'test';
    final public const REQUEST_ACTION_GET_EXPRESS_CHECKOUT_DETAILS = 'getexpresscheckoutdetails';
    final public const REQUEST_ACTION_SET_EXPRESS_CHECKOUT = 'setexpresscheckout';
    final public const REQUEST_ACTION_PAYOLUTION_PRE_CHECK = 'pre-check';
    final public const REQUEST_ACTION_PAYOLUTION_CALCULATION = 'calculation';
    final public const REQUEST_ACTION_GENERIC_PAYMENT = 'genericpayment';
    final public const REQUEST_ACTION_CREDITCARD_CHECK = 'creditcardcheck';
    final public const REQUEST_ACTION_GET_FILE = 'getfile';
    final public const REQUEST_ACTION_MANAGE_MANDATE = 'managemandate';
    final public const REQUEST_ACTION_DEBIT = 'debit';
    final public const REQUEST_ACTION_RATEPAY_PROFILE = 'ratepayProfile';
    final public const REQUEST_ACTION_RATEPAY_CALCULATION = 'ratepayCalculation';
    final public const REQUEST_ACTION_SECURED_INSTALLMENT_OPTIONS = 'securedInstallmentOptions';

    final public const CLEARING_TYPE_DEBIT = 'elv';
    final public const CLEARING_TYPE_WALLET = 'wlt';
    final public const CLEARING_TYPE_FINANCING = 'fnc';
    final public const CLEARING_TYPE_CREDIT_CARD = 'cc';
    final public const CLEARING_TYPE_PREPAYMENT = 'vor';
    final public const CLEARING_TYPE_ONLINE_BANK_TRANSFER = 'sb';
    final public const CLEARING_TYPE_INVOICE = 'rec';

    abstract public function getRequestParameter(AbstractRequestParameterStruct $arguments): array;

    /**
     * Returns true if builder is meant to build parameters for the given action
     */
    abstract public function supports(AbstractRequestParameterStruct $arguments): bool;

    protected function getOrderCurrency(?OrderEntity $order, Context $context): CurrencyEntity
    {
        if ($order !== null && $order->getCurrency() !== null) {
            return $order->getCurrency();
        }

        if (property_exists($this, 'currencyRepository') === false) {
            throw new \RuntimeException('currency repository injection missing');
        }

        $currencyId = $context->getCurrencyId();

        if ($order !== null) {
            $currencyId = $order->getCurrencyId();
        }

        $criteria = new Criteria([$currencyId]);

        /** @var CurrencyEntity|null $currency */
        $currency = $this->currencyRepository->search($criteria, $context)->first();

        if ($currency === null) {
            throw new \RuntimeException('missing order currency entity');
        }

        return $currency;
    }

    /**
     * throws an exception if the given $method does not exist on the given $object
     */
    protected function validateMethod(object $object, string $method): void
    {
        if (!method_exists($object, $method)) {
            // there is no function to get the salesChannelContext. Without it the builder is not able to get the customer data
            throw new \RuntimeException(sprintf('method `%s` does not exist on %s', $method, $object::class));
        }
    }

    protected function applyPhoneParameter(OrderEntity $order, array &$parameters, string $submittedPhoneNumber, Context $context): void
    {
        if (property_exists($this, 'customerRepository') === false) {
            throw new \RuntimeException('customer repository injection missing');
        }

        if (!$order->getOrderCustomer()) {
            throw new \RuntimeException('missing order customer');
        }

        $customer = $order->getOrderCustomer()->getCustomer();

        if (!$customer) {
            throw new \RuntimeException('missing customer');
        }

        $customerCustomFields = $customer->getCustomFields() ?? [];
        $customFieldPhoneNumber = $customerCustomFields[CustomFieldInstaller::CUSTOMER_PHONE_NUMBER] ?? null;

        if (!empty($submittedPhoneNumber) && $submittedPhoneNumber !== $customFieldPhoneNumber) {
            // Update the phone number that is stored at the customer
            $customFieldPhoneNumber = $submittedPhoneNumber;
            $customerCustomFields[CustomFieldInstaller::CUSTOMER_PHONE_NUMBER] = $customFieldPhoneNumber;
            $this->customerRepository->update(
                [
                    [
                        'id' => $customer->getId(),
                        'customFields' => $customerCustomFields,
                    ],
                ],
                $context
            );
        }

        if (!$customFieldPhoneNumber) {
            throw new \RuntimeException('missing phone number');
        }

        $parameters['telephonenumber'] = $customerCustomFields[CustomFieldInstaller::CUSTOMER_PHONE_NUMBER];
    }

    protected function applyBirthdayParameter(OrderEntity $order, array &$parameters, string $submittedBirthday, Context $context): void
    {
        if (property_exists($this, 'customerRepository') === false) {
            throw new \RuntimeException('customer repository injection missing');
        }

        if (!$order->getOrderCustomer()) {
            throw new \RuntimeException('missing order customer');
        }

        $customer = $order->getOrderCustomer()->getCustomer();

        if (!$customer) {
            throw new \RuntimeException('missing customer');
        }

        $customerCustomFields = $customer->getCustomFields() ?? [];
        $customFieldBirthday = $customerCustomFields[CustomFieldInstaller::CUSTOMER_BIRTHDAY] ?? null;

        if (!empty($submittedBirthday) && $submittedBirthday !== $customFieldBirthday) {
            // Update the birthday that is stored at the customer
            $customFieldBirthday = $submittedBirthday;
            $customerCustomFields[CustomFieldInstaller::CUSTOMER_BIRTHDAY] = $customFieldBirthday;
            $this->customerRepository->update(
                [
                    [
                        'id' => $customer->getId(),
                        'customFields' => $customerCustomFields,
                    ],
                ],
                $context
            );
        }

        if (!$customFieldBirthday) {
            throw new \RuntimeException('missing birthday');
        }

        $birthday = \DateTime::createFromFormat('Y-m-d', $customFieldBirthday);
        if ($birthday instanceof \DateTimeInterface) {
            $parameters['birthday'] = $birthday->format('Ymd');
        }
    }
}
