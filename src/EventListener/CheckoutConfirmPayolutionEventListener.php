<?php

declare(strict_types=1);

namespace PayonePayment\EventListener;

use PayonePayment\Components\ConfigReader\ConfigReaderInterface;
use PayonePayment\PaymentMethod\PayonePayolutionInstallment;
use PayonePayment\PaymentMethod\PayonePayolutionInvoicing;
use Shopware\Core\Checkout\Payment\PaymentMethodCollection;
use Shopware\Core\Checkout\Payment\PaymentMethodEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\Account\Order\AccountEditOrderPageLoadedEvent;
use Shopware\Storefront\Page\Account\PaymentMethod\AccountPaymentMethodPageLoadedEvent;
use Shopware\Storefront\Page\Checkout\Confirm\CheckoutConfirmPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheckoutConfirmPayolutionEventListener implements EventSubscriberInterface
{
    public function __construct(private readonly ConfigReaderInterface $configReader)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckoutConfirmPageLoadedEvent::class => 'hidePaymentMethodsForCompanies',
            AccountPaymentMethodPageLoadedEvent::class => 'hidePaymentMethodsForCompanies',
            AccountEditOrderPageLoadedEvent::class => 'hidePaymentMethodsForCompanies',
        ];
    }

    public function hidePaymentMethodsForCompanies(
        CheckoutConfirmPageLoadedEvent|AccountPaymentMethodPageLoadedEvent|AccountEditOrderPageLoadedEvent $event
    ): void {
        $page = $event->getPage();

        if (!$this->customerHasCompanyAddress($event->getSalesChannelContext())) {
            return;
        }

        $paymentMethods = $page->getPaymentMethods();

        $paymentMethods = $this->removePaymentMethod($paymentMethods, PayonePayolutionInstallment::UUID);

        if ($this->companyDataHandlingIsDisabled($event->getSalesChannelContext())) {
            $paymentMethods = $this->removePaymentMethod($paymentMethods, PayonePayolutionInvoicing::UUID);
        }

        $page->setPaymentMethods($paymentMethods);
    }

    private function removePaymentMethod(PaymentMethodCollection $paymentMethods, string $paymentMethodId): PaymentMethodCollection
    {
        return $paymentMethods->filter(
            static fn (PaymentMethodEntity $paymentMethod) => $paymentMethod->getId() !== $paymentMethodId
        );
    }

    private function customerHasCompanyAddress(SalesChannelContext $context): bool
    {
        $customer = $context->getCustomer();

        if ($customer === null) {
            return false;
        }

        $billingAddress = $customer->getActiveBillingAddress();

        if ($billingAddress === null) {
            return false;
        }

        return !empty($billingAddress->getCompany());
    }

    private function companyDataHandlingIsDisabled(SalesChannelContext $context): bool
    {
        $configuration = $this->configReader->read($context->getSalesChannel()->getId());

        return !($configuration->get('payolutionInvoicingTransferCompanyData'));
    }
}
