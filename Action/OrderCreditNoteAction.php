<?php

namespace CreditNote\Action;

use CreditNote\CreditNote;
use CreditNote\Event\OrderCreditNoteEvent;
use CreditNote\Event\OrderCreditNoteEvents;
use CreditNote\Model\OrderCreditNote;
use Propel\Runtime\Propel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Condition\ConditionCollection;
use Thelia\Condition\ConditionFactory;
use Thelia\Condition\Implementation\ConditionInterface;
use Thelia\Condition\Operators;
use Thelia\Core\Event\Coupon\CouponCreateOrUpdateEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Coupon\Type\CouponInterface;
use Thelia\Model\CouponQuery;
use Thelia\Model\LangQuery;
use Thelia\Model\Order;
use Thelia\Model\OrderQuery;

/**
 * Actions on order credit notes.
 */
class OrderCreditNoteAction implements EventSubscriberInterface
{
    /**
     * Event dispatcher.
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * The coupon type to use.
     * @var CouponInterface
     */
    protected $couponType;

    /**
     * Coupon condition factory.
     * @var ConditionFactory
     */
    protected $conditionFactory;

    /**
     * The customer restriction condition type.
     * @var ConditionInterface
     */
    protected $customerConditionType;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        CouponInterface $couponType,
        ConditionFactory $conditionFactory,
        ConditionInterface $customerConditionType
    ) {
        $this->dispatcher = $dispatcher;
        $this->couponType = $couponType;
        $this->conditionFactory = $conditionFactory;
        $this->customerConditionType = $customerConditionType;
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderCreditNoteEvents::CREATE => ["create", 128],
        ];
    }

    public function create(OrderCreditNoteEvent $event)
    {
        $con = Propel::getConnection();
        $con->beginTransaction();

        $defaultLocale = LangQuery::create()->findOneByByDefault(true)->getLocale();

        // credit note
        $orderCreditNote = new OrderCreditNote();

        if (null !== $orderId = $event->getOrderId()) {
            $orderCreditNote->setOrderId($orderId);
            $order = OrderQuery::create()->findPk($orderId);
        } else {
            throw new \RuntimeException("Missing order id when creating an order credit note.");
        }

        if (!$order->isPaid()) {
            throw new \RuntimeException("Cannot create an order credit note for an unpaid order.");
        }

        if (null !== $amount = $event->getAmount()) {
            $orderCreditNote->setAmount($amount);
        }

        if (null !== $message = $event->getMessage()) {
            $orderCreditNote->setMessage($message);
        }

        // coupon for this credit note
        $couponCreationEvent = new CouponCreateOrUpdateEvent(
            // code
            $this->getUniqueCouponCode($order),
            // serviceId
            $this->couponType->getServiceId(),
            // title
            Translator::getInstance()->trans(
                "Credit note for order %order_ref",
                [
                    "%order_ref" => $order->getRef(),
                ],
                CreditNote::MESSAGE_DOMAIN_BO,
                $defaultLocale
            ),
            // effects
            $this->couponType->getEffects([
                "coupon_specific" => [
                    "amount" => $amount,
                ],
            ]),
            // shortDescription
            "",
            // description
            "",
            // isEnabled
            true,
            // expirationDate
            (new \DateTime())->add(
                new \DateInterval("P" . CreditNote::getConfigValue(CreditNote::CONF_KEY_COUPON_CODE_DURATION) . "D")
            ),
            // isAvailableOnSpecialOffers
            true,
            // isCumulative
            true,
            // isRemovingPostage
            false,
            // maxUsage
            1,
            // locale
            $defaultLocale,
            // freeShippingForCountries
            [],
            // freeShippingForMethods
            [],
            // perCustomerUsageCount
            0
        );

        // restrict the coupon to the original order customer if this option is enabled
        if (CreditNote::getConfigValue(CreditNote::CONF_KEY_COUPON_CODE_RESTRICTED_TO_ORIGINAL_CUSTOMER)) {
            $customerCondition = $this->conditionFactory->build(
                $this->customerConditionType->getServiceId(),
                ["customers" => Operators::IN],
                ["customers" => [$order->getCustomerId()]]
            );

            $conditions = new ConditionCollection();
            $conditions[] = $customerCondition;
            $couponCreationEvent->setConditions($conditions);
        }

        // create the coupon and then the credit note
        try {
            $this->dispatcher->dispatch(TheliaEvents::COUPON_CREATE, $couponCreationEvent);
            if (null !== $couponCreationEvent->getConditions()) {
                $this->dispatcher->dispatch(TheliaEvents::COUPON_CONDITION_UPDATE, $couponCreationEvent);
            }

            $orderCreditNote->setCouponId($couponCreationEvent->getCouponModel()->getId());
            $orderCreditNote->save($con);

            $con->commit();
        } catch (\Exception $e) {
            $con->rollback();

            throw $e;
        }

        $event->setOrderCreditNote($orderCreditNote);
    }

    /**
     * Build an unique code for a credit note coupon.
     * @param Order $order Order for which the credit note is generated.
     * @return string
     */
    protected function getUniqueCouponCode(Order $order)
    {
        $baseCouponCode = CreditNote::getConfigValue(CreditNote::CONF_KEY_COUPON_CODE_PREFIX) . "-" . $order->getRef();
        $couponCode = $baseCouponCode;
        $suffix = 0;

        while (null !== CouponQuery::create()->findOneByCode($couponCode)) {
            ++$suffix;
            $couponCode = $baseCouponCode . "-" . $suffix;
        }

        return $couponCode;
    }
}
