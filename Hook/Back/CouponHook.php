<?php

namespace CreditNote\Hook\Back;

use Thelia\Core\Event\Hook\HookRenderEvent;
use Thelia\Core\Hook\BaseHook;

/**
 * Coupon related back-office hooks.
 */
class CouponHook extends BaseHook
{
    /**
     * Add a header for order links in the coupon table.
     * @param HookRenderEvent $event
     */
    public function onCouponTableHeader(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                "order-credit-note-coupon-table-header.html"
            )
        );
    }

    /**
     * Add an order link to the coupon table rows if the coupon is an order credit note coupon.
     * @param HookRenderEvent $event
     */
    public function onCouponTableRow(HookRenderEvent $event)
    {
        $event->add(
            $this->render(
                "order-credit-note-coupon-table-row.html",
                [
                    "coupon_id" => $event->getArgument("coupon_id"),
                ]
            )
        );
    }
}
