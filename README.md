# CreditNote

Grant credit notes to customers, generating a coupon code.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is CreditNote.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/credit-note-module:~1.0
```

## Usage

From an order page in the back-office, in the modules tab, you can grant a new credit note by inputting an amount
(in the order's original currency) and optionally a message for the customer.
A coupon code will be created that can be redeemed by the customer for this amount.
An email will be sent to the customer to notify him.

Existing credit notes are displayed in several places:
 
- the order page list all existing credit notes for this order
- the total amount granted in credit notes is displayed in the orders list
- a link to the corresponding order is added to the coupons list, if the coupon is a credit note

## Configuration

Some options are available in the module configuration page:

- the validity period of the generated coupons (default: 365 days)
- the prefix of the generated coupon codes (default: CREDIT)
- whether the generated coupons should be restricted to the customer that initially placed the order (default: yes)

## Actions

### Create a credit note from an order

```PHP
use CreditNote\Event\OrderCreditNoteEvent;
use CreditNote\Event\OrderCreditNoteEvents;

$event = (new OrderCreditNoteEvent())
    ->setOrderId($myOrder->getId())
    ->setAmount(20) // in the order's currency
    ->setMessage("Refund");

$dispatcher->dispatch(OrderCreditNoteEvents::CREATE, $event);
```

## Loop

### `order-credit-note`

Lists credit notes on orders.

#### Input arguments

|Argument     |Description                                   |
|-------------|----------------------------------------------|
|**id**       | Id or list of ids of credit notes            |
|**order_id** | Id or list of ids of the associated order(s) |
|**coupon_id**| Id or list of ids of the generated coupon(s) |
|**order**    | Order of the results                         |
|**lang**     | Locale of the results                        |

Where order is one of the following:
- `id` *(default)*
- `id-reverse`
- `order_id`
- `order_id-reverse`
- `coupon_id`
- `coupon_id-reverse`
- `amount`
- `amount-reverse`
- `message`
- `message-reverse`

#### Output arguments

|Variable  |Description                                              |
|----------|---------------------------------------------------------|
|$ID       | Id                                                      |
|$ORDER_ID | Id of the associated order                              |
|$COUPON_ID| Id of the generated coupon                              |
|$AMOUNT   | Credit note amount (in the associated order's currency) |
|$MESSAGE  | Localized message associated to the credit note         |
|$LOCALE   | Locale of the results                                   |

## Mail templates

### `order_credit_note_created_notify_customer`

Template used to notify customers that they have been granted a new credit note from an order.
