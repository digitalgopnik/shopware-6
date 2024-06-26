<?php

declare(strict_types=1);

namespace PayonePayment\Payone\RequestParameter\Builder\SecuredInstallment;

use PayonePayment\Components\Currency\CurrencyPrecisionInterface;
use PayonePayment\Components\DeviceFingerprint\AbstractDeviceFingerprintService;
use PayonePayment\Components\Helper\OrderFetcherInterface;
use PayonePayment\Components\Hydrator\LineItemHydrator\LineItemHydratorInterface;
use PayonePayment\PaymentHandler\AbstractPayonePaymentHandler;
use PayonePayment\PaymentHandler\PayoneSecuredInstallmentPaymentHandler;
use PayonePayment\Payone\RequestParameter\Builder\AbstractRequestParameterBuilder;
use PayonePayment\Payone\RequestParameter\Struct\AbstractRequestParameterStruct;
use PayonePayment\Payone\RequestParameter\Struct\PaymentTransactionStruct;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

class AuthorizeRequestParameterBuilder extends AbstractRequestParameterBuilder
{
    public function __construct(
        protected OrderFetcherInterface $orderFetcher,
        protected AbstractDeviceFingerprintService $deviceFingerprintService,
        protected CurrencyPrecisionInterface $currencyPrecision,
        protected LineItemHydratorInterface $lineItemHydrator,
        protected EntityRepository $customerRepository
    ) {
    }

    /**
     * @param PaymentTransactionStruct $arguments
     */
    public function getRequestParameter(AbstractRequestParameterStruct $arguments): array
    {
        $dataBag = $arguments->getRequestData();
        $salesChannelContext = $arguments->getSalesChannelContext();
        $context = $salesChannelContext->getContext();
        $paymentTransaction = $arguments->getPaymentTransaction();
        $order = $this->getOrder($paymentTransaction->getOrder()->getId(), $context);
        $customer = $order->getOrderCustomer();
        $currency = $this->getOrderCurrency($order, $context);

        if ($customer === null) {
            throw new \RuntimeException('missing order customer');
        }

        $parameters = [
            'clearingtype' => self::CLEARING_TYPE_FINANCING,
            'financingtype' => AbstractPayonePaymentHandler::PAYONE_FINANCING_PIN,
            'request' => self::REQUEST_ACTION_AUTHORIZE,
            'add_paydata[device_token]' => $this->deviceFingerprintService->getDeviceIdentToken($salesChannelContext),
            'amount' => $this->currencyPrecision->getRoundedTotalAmount($order->getAmountTotal(), $currency),
            'currency' => $currency->getIsoCode(),
            'bankaccountholder' => $customer->getFirstName() . ' ' . $customer->getLastName(),
            'iban' => $dataBag->get('securedInstallmentIban'),
            'add_paydata[installment_option_id]' => $dataBag->get('securedInstallmentOptionId'),
        ];

        if ($order->getLineItems() !== null) {
            $parameters = array_merge($parameters, $this->lineItemHydrator->mapOrderLines($currency, $order, $context));
        }

        $this->applyPhoneParameter($order, $parameters, $dataBag->get('securedInstallmentPhone') ?? '', $context);
        $this->applyBirthdayParameter($order, $parameters, $dataBag->get('securedInstallmentBirthday') ?? '', $context);

        return $parameters;
    }

    public function supports(AbstractRequestParameterStruct $arguments): bool
    {
        if (!($arguments instanceof PaymentTransactionStruct)) {
            return false;
        }

        $paymentMethod = $arguments->getPaymentMethod();
        $action = $arguments->getAction();

        return $paymentMethod === PayoneSecuredInstallmentPaymentHandler::class && $action === self::REQUEST_ACTION_AUTHORIZE;
    }

    protected function getOrder(string $orderId, Context $context): OrderEntity
    {
        // Load order to make sure all associations are set
        $order = $this->orderFetcher->getOrderById($orderId, $context);

        if ($order === null) {
            throw new \RuntimeException('missing order');
        }

        return $order;
    }
}
