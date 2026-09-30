<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support;

use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteAddress;
use CreditNote\Model\CreditNoteDetail;
use CreditNote\Model\CreditNoteStatusQuery;
use CreditNote\Model\CreditNoteTypeQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Model\Customer;
use Thelia\Model\ModuleConfigQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderProduct;
use Thelia\Model\OrderProductTax;
use Thelia\Model\OrderStatus;
use Thelia\Test\FixtureFactory;

/**
 * Writes the rows a credit note test needs, inside the test transaction: a customer with an
 * order and one product line, and credit notes the way the back-office writes them (their own
 * copy of the invoice address, one line per refunded product at the unit prices of the order,
 * the totals summed from the lines).
 */
final class CreditNoteFixture
{
    private readonly FixtureFactory $factory;

    public function __construct(private readonly ConnectionInterface $connection)
    {
        $this->factory = new FixtureFactory($connection);

        // The module configuration (the numbering counters) is cached in a static for the
        // whole process: after the rollback of a previous test, the cache still holds the
        // counters that test moved. Read them again from the database.
        ModuleConfigQuery::resetConfigCache();
    }

    public function customer(): Customer
    {
        return $this->factory->customer($this->factory->customerTitle(), ['firstname' => 'Mohamed', 'lastname' => 'Diallo']);
    }

    /**
     * A paid order with one product line, 98.00 without tax and 117.60 with tax, quantity 2.
     */
    public function paidOrder(Customer $customer): Order
    {
        $order = $this->factory->order($customer, ['statusCode' => OrderStatus::CODE_PAID]);

        $orderProduct = (new OrderProduct())
            ->setOrderId($order->getId())
            ->setProductRef('PROD013')
            ->setProductSaleElementsRef('PROD013-0')
            ->setTitle('Violet')
            ->setQuantity(2)
            ->setPrice('98.000000')
            ->setPromoPrice('0.000000')
            ->setWasNew(0)
            ->setWasInPromo(0)
            ->setTaxRuleTitle('VAT 20%');
        $orderProduct->save($this->connection);

        (new OrderProductTax())
            ->setOrderProductId($orderProduct->getId())
            ->setTitle('VAT 20%')
            ->setAmount('19.600000')
            ->setPromoAmount('0.000000')
            ->save($this->connection);

        return $order;
    }

    public function address(Customer $customer): CreditNoteAddress
    {
        $address = (new CreditNoteAddress())
            ->setCustomerTitleId($customer->getTitleId())
            ->setFirstname($customer->getFirstname())
            ->setLastname($customer->getLastname())
            ->setAddress1('3 rue de Paris')
            ->setZipcode('93100')
            ->setCity('Montreuil')
            ->setCountryId($this->factory->country()->getId());
        $address->save($this->connection);

        return $address;
    }

    /**
     * A credit note on the given order, refunding the given quantity of its first line, in the
     * status named by code ('proposed', 'accepted'...). Not saved: the caller saves it, so the
     * numbering the module does on save can be observed.
     */
    public function creditNote(Customer $customer, ?Order $order, string $statusCode = 'proposed', int $quantity = 1, string $typeCode = 'back_product'): CreditNote
    {
        $status = CreditNoteStatusQuery::create()->findOneByCode($statusCode)
            ?? throw new \RuntimeException(\sprintf('The credit note status "%s" is not seeded.', $statusCode));
        $type = CreditNoteTypeQuery::create()->findOneByCode($typeCode)
            ?? throw new \RuntimeException(\sprintf('The credit note type "%s" is not seeded.', $typeCode));

        $creditNote = (new CreditNote())
            ->setCreditNoteAddress($this->address($customer))
            ->setCustomerId($customer->getId())
            ->setOrderId($order?->getId())
            ->setTypeId($type->getId())
            ->setStatusId($status->getId())
            ->setCurrencyId($order?->getCurrencyId() ?? $this->factory->currency()->getId())
            ->setCurrencyRate(1.0)
            ->setDiscountWithoutTax('0')
            ->setDiscountWithTax('0')
            ->setAllowPartialUse(true);

        if (null !== $order) {
            $orderProduct = $order->getOrderProducts()->getFirst();

            $creditNote->addCreditNoteDetail((new CreditNoteDetail())
                ->setOrderProductId($orderProduct->getId())
                ->setTitle($orderProduct->getTitle())
                ->setType('product')
                ->setQuantity($quantity)
                ->setPrice('98.000000')
                ->setPriceWithTax('117.600000'));
            $creditNote
                ->setTotalPrice(\sprintf('%.6F', 98 * $quantity))
                ->setTotalPriceWithTax(\sprintf('%.6F', 117.6 * $quantity));
        } else {
            $creditNote->addCreditNoteDetail((new CreditNoteDetail())
                ->setTitle('Commercial gesture')
                ->setType('free')
                ->setQuantity(1)
                ->setPrice('10.000000')
                ->setPriceWithTax('12.000000'));
            $creditNote->setTotalPrice('10.000000')->setTotalPriceWithTax('12.000000');
        }

        return $creditNote;
    }

    public function factory(): FixtureFactory
    {
        return $this->factory;
    }
}
