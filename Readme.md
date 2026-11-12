# Credit Note

Author: Gilles Bourgeat <gilles.bourgeat@gmail.com>

* Allows granting credit notes to customers, who pay their next orders with them

## Compatibility

| Module | Thelia |
|--------|--------|
| 5.x | 3.3 and later (PHP 8.3 and later, Twig back-office `default-twig`) |
| 4.x | 3.2 and later (PHP 8.3 and later, Twig back-office `default-twig`) |
| 3.0.x | 2.5 |
| 2.3.x | 2.3 and 2.4 |

The credit note document is printed by the PDF template: `thelia/pdf-default-template` 1.2
or later ships it (`credit-note.html.twig`). The lines "Paid by credit note" on the invoice of
an order need the version of the template that follows 1.4.1.

From 5.0 the module is a payment module: it needs the payment in several parts of Thelia 3.3.
The module type changes by itself when the shop refreshes its modules (`php Thelia module:refresh`),
and the module then shows in the list of payment modules of the back-office. The choice of the
credit notes on the payment step is drawn for the Flexy theme, which needs Symfony UX Live
Component; without it that step is left out and the rest of the module works.

## Installation

```
composer require thelia/credit-note-module:^4.0
```

Then activate the module in the back-office, or with `php Thelia module:activate CreditNote`.
For Thelia 2.5, require `~3.0.1`; before Thelia 2.5, `~2.3.0`.

## Back-office

### Rights

A credit note is an amount granted to a customer. Every action on credit notes takes the
right on the orders (`admin.order`) **and** on the module, with the access the action needs:

| Action | Access |
|--------|--------|
| Credit note list, modal of a credit note, its document, customer and order search | VIEW |
| Create or update a credit note | UPDATE |
| Delete a credit note that is not numbered yet | DELETE |

The configuration of the module takes the modules resource (`admin.module`, UPDATE). An
administrator without the VIEW access sees neither the "Credit Notes" menu, nor the tab of
the order page, nor the block of the customer page.

### Balance and use of a credit note

The credit note list, the tab of an order and the block of a customer show what is left of each
accepted credit note in a "Balance" column: its total with tax minus what orders were paid with.
A proposed or refused credit note shows none. The modal of a credit note shows the same balance
and the orders it paid, with the amount each one took.

The modal also sets how the customer may spend the credit note: over several purchases, or in a
single purchase for its whole balance. The choice is open until an order is paid with the credit
note, then it is shown and no longer changed.

The page of an order paid with credit notes lists them under its lines, with the amount each one
paid and what was left to the payment module.

### Administration log

Creating, updating or deleting a credit note writes a line in the administration log
(`admin_log`, resource `admin.order`), on the order the credit note refunds when it has one:
its reference, its status, its total with tax, its order and its customer.

### Document from the command line

```
php Thelia credit-note:pdf:render CN00000012 --out credit-note.pdf
```

Writes the document of an accepted credit note, named by its id or its reference, to a file
(`credit-note-<invoice reference>.pdf` by default), without the HTTP flow: to preview it or to
work on its template. It is the document the back-office serves, in the language of the
credit note.

## Paying an order with credit notes

A customer pays part of an order, or the whole of it, with their accepted credit notes. On the
payment step of the checkout, the block "Pay part of your order with" lists the credit notes that
have something left, one box each. Ticking a box puts the credit note on the cart; the step then
shows the amount it pays and what is left to pay.

- Several credit notes can pay the same order. The ones to spend in a single purchase are spent
  first, then the others, the oldest first, until the order is covered.
- A credit note pays in its own currency only: it is not offered on a cart in another currency,
  and no rate is applied.
- A credit note never pays more than the order is worth. What the order did not need stays on the
  credit note for a next purchase.
- A credit note to spend in a single purchase is refused, with a message, on a cart worth less
  than it. If the cart shrinks after it was chosen, it is taken off the cart and the customer is
  told.
- A credit note can change after it was chosen: spent on another cart of the customer, or no
  longer accepted. When it can no longer pay anything, it is taken off the cart and the
  customer is told. When part of it was spent elsewhere, it stays on the cart for what is left.
- The order is placed for what the payment step showed. If the credit notes of the cart changed
  while the customer was looking at the step, the order is refused once: the customer comes back
  to the payment step with a message, reads the new amounts, and confirms again.
- When the credit notes cover the whole order, it is paid without any payment provider. When they
  do not, the customer chooses a payment module for the rest. That module must be able to charge
  a balance (`Thelia\Module\BalanceChargingPaymentModuleInterface`); Thelia refuses to combine a
  credit note with a module that charges the whole total.
- A customer group restricted to some payment modules must have the module in its list to pay with
  credit notes.
- Cancelling the order gives the credit notes back: their balance returns, and a credit note that
  was used is accepted again.
- Refunding the order gives nothing back. Thelia releases the parts of a payment on cancellation
  only, so the merchant grants a new credit note when a refunded order had been paid with one.

Using a credit note is a payment, never a discount: the invoice of the order keeps its full total
and its taxes, and prints one line per credit note under it.

### Credit note or wallet

A credit note is a document. It is granted by the merchant, usually against an order (a return, a
billing error, a refund), it carries a reference and an accounting number, it is printed, and the
credit notes of an order cannot exceed what the order was worth. Use it when the shop owes a
customer an amount it must be able to justify.

A wallet is a running balance: loyalty points or credit earned on purchases, written as movements
that may expire, with no document for each one. Use a wallet module for rewards. The module does
not turn a credit note into wallet credit, and a credit note is not the place to record a reward.

## Customer account

A signed-in customer sees their credit notes in their account, through two theme hook
points of the Flexy theme (`theme_hook()`), without any change to the theme:

- `account.bottom`, at the bottom of the account page: every accepted credit note of the
  customer, with its reference, its date, the order it refunds, the amount it was issued
  for and what is left of it (the total with tax minus what the orders it was used on
  consumed, read from `order_credit_note`), whether it can be used in several purchases,
  and a download button;
- `account-order.bottom`, at the bottom of an order page: the credit notes attached to
  that order.

A proposed or refused credit note is a draft of the merchant and is not shown. Nothing is
rendered for a visitor, for a customer without credit note, or for an order that does not
belong to the signed-in customer.

The document is printed in the language the customer prefers when it is French or English,
otherwise in the default language of the shop, and in English when the shop has none
(`CreditNoteDocumentLocale`); the back-office route prints the same document. The PDF theme
receives that language as `document_locale`, with the title of the credit note type in it.

The document is served by `GET /account/credit-note/{id}/pdf`: the customer must be signed
in (a visitor is sent to the login page), own the credit note, and the credit note must be
accepted, otherwise the route answers 404 whatever the reason.

The fragment is `templates/frontOffice/default/CreditNote/account-credit-notes.html.twig`;
a theme overrides it at `templates/frontOffice/<theme>/modules/CreditNote/account-credit-notes.html.twig`
(variables: `hook`, `credit_notes`, `texts`). Its labels live in the `creditnote.fo.default`
domain (`I18n/frontOffice/default/`). The fragment of the payment step is
`checkout-payment-part.html.twig`, overridden the same way.

Amounts are written as the theme writes its own: the currency by its code, laid out for the
language of the page ("€200.00" in English, "200,00 €" in French). The back-office does the same
in the language of the administrator.

## API

The module adds a `CreditNote` resource to the API of Thelia. Amounts are decimal strings with six
decimals.

| Operation | Who | What |
|-----------|-----|------|
| `GET /api/front/account/credit-notes` | signed-in customer | their accepted and used credit notes, paginated |
| `GET /api/front/account/credit-notes/{id}` | signed-in customer | one of their credit notes |
| `GET /api/admin/credit_notes` | administrator, VIEW | every credit note, paginated, filtered by `customerId`, `orderId`, `status` (code) and `ref` (part of the reference) |
| `GET /api/admin/credit_notes/{id}` | administrator, VIEW | one credit note with its lines |
| `POST /api/admin/credit_notes` | administrator, UPDATE | creates a credit note from an order |

The customer is the one of the token, never a parameter. The credit note of another customer and
a draft of the merchant answer 404, like an id that does not exist.

The administration operations take the same rights as the back-office: the right on the orders
and the right on the module, both.

| Field | Read by | What |
|-------|---------|------|
| `id`, `ref` | customer, administrator | the credit note and its reference |
| `invoiceRef`, `invoiceDate` | customer, administrator | its accounting number and the date of its document, once accepted |
| `status` | customer, administrator | `proposed`, `accepted`, `refused` or `used` |
| `orderRef`, `currency` | customer, administrator | the reference of its order and the code of its currency |
| `totalPriceWithTax` | customer, administrator | what it was granted for |
| `balance` | customer, administrator | what is left to spend; absent for a credit note that is not accepted or used |
| `allowPartialUse` | customer, administrator | `false` when it is spent in a single purchase |
| `type`, `customerId`, `orderId`, `totalPrice`, `createdAt` | administrator | |
| `lines` | administrator, on one credit note | `orderProductId`, `title`, `type` (`product` or `shipping`), `quantity`, `price`, `priceWithTax` |

The lists are paginated with `page` and `itemsPerPage`, the newest credit note first.

| Answer | When |
|--------|------|
| 200 | a list or a credit note |
| 201 | a credit note created |
| 401 | no token |
| 403 | an administrator without the right on the orders or on the module, or a customer on the administration |
| 404 | a credit note that does not exist, or that the customer may not read |
| 422 | a creation that cannot make a credit note; nothing is written |

```
POST /api/admin/credit_notes
{
    "orderId": 42,
    "type": "back_product",
    "status": "accepted",
    "lines": [{"orderProductId": 118, "quantity": 1}],
    "allowPartialUse": true,
    "comment": "Returned by post"
}
```

The customer, the currency and the invoice address are the ones of the order, and each line is
refunded at the price the order was invoiced. `status` is `proposed` (the default) or `accepted`.
The type `order_full_refund` takes every line, the postage and the discount of the order, and
reads no `lines`. The request is answered 422, and nothing is written, when the order does not
exist or is not invoiced (not paid, or cancelled), when a line is not one of the order or is given
twice, when a quantity is not a positive integer or exceeds what the line holds once the other
credit notes of the order are counted, or when the credit notes of the order would refund more
than it was worth. A creation writes a line in the
administration log.

## Guarantees

- **Ceiling of an order.** The credit notes of an order never refund more than the order was
  worth: when a credit note is written (whatever writes it: back-office, API, a command), the
  total with tax of the other non-refused credit notes of the order plus its own must stay
  within the total of the order, postage and discount included (`CreditNoteOrderCeiling`,
  enforced by the numbering listener before any number is drawn). The back-office shows the
  refusal as an error message on the order page.
  The ceiling is a setting of the module (`order_ceiling`, on by default, a checkbox on the
  configuration screen): a shop that grants commercial gestures or refunds the return postage
  beyond the order turns it off, and credit notes are then written whatever the order was worth.
- **A line is refunded once.** The credit notes of an order never give back more units of one of
  its lines than the line holds, whatever the amounts and whether the ceiling is on or off
  (`CreditNoteOrderLines`, checked with the ceiling). A refused credit note gives nothing back.
  A gesture beyond the order is a free amount on the credit note, not a second refund of a line.
- **Unique numbering.** The reference and the accounting number come from counters that only
  move forward and are never reused, even after a deletion; the database refuses a duplicate
  of either (`ref_UNIQUE`, `invoice_ref_UNIQUE`). A counter is read from the database under a
  lock held until the credit note is written (`CreditNoteCounter`): two credit notes created at
  the same time draw two numbers, the second one after the first. The configuration screen
  moves a counter forward only: saving it with the value it showed when it was opened leaves
  the numbers drawn meanwhile alone, with a message.
  A shop that numbers its credit notes like its invoices (`invoice_ref_with_thelia_order`) draws
  the number from the series of InvoiceRef 3.1 or later (`InvoiceRefSequence::next()`), under the
  same lock as the orders: an invoice and a credit note accepted at the same time never share a
  counter value, and a number an order already carries is skipped.
- **Atomic use.** `CreditNoteConsumption::consume()` writes the use of a credit note on an
  order inside a transaction that locks the credit note row (`SELECT … FOR UPDATE`) and reads
  the balance again under the lock: two orders paid at the same time with the same credit
  note consume its balance once, the second one waits and is refused when nothing is left.
  It also refuses a credit note that is not accepted, already used, owned by another
  customer, already used on that order, a partial use of a credit note that forbids it, and
  marks the credit note used when its balance reaches zero. The payment of an order writes
  the same use inside the transaction of Thelia, with the payment part it settles, and gives it
  back once when that part is released.
- **Ceiling under a lock.** The order row is locked while the ceiling is checked, until the
  credit note is written: two credit notes created at the same time on one order are counted
  one after the other. What the other credit notes grant is read under that lock, as it is
  committed at that moment. The back-office form and the API apply the same rules to a credit note
  made from an order (`CreditNoteOrderRules`).
- **Amounts to the cent.** Balances, uses and the ceiling are compared in cents, as integers,
  with no tolerance, each amount rounded to the cent once and the same way.
- **One order for the locks.** Credit notes are locked in the order they are spent in, when an
  order is paid and when its payment is given back: a payment and a cancellation that touch the
  same credit notes never wait on each other.
- **Token on the back-office writes.** Creating, updating and deleting a credit note from the
  back-office take the token the pages of the module carry (`csrf_token('credit_note')`, sent as
  `_token`) and answer 403 without it: a page of another site cannot make the browser of a
  signed-in administrator write a credit note. A page or a script of the shop that posts to
  these routes must send it.
- **Credit notes of the customer only.** A credit note is put on a cart, and spent, only by the
  customer it was granted to: checked when it is chosen and again under the lock when the order
  is paid. The amount paid comes from the balance read under that lock, never from the browser.
- **No surprise at payment.** A credit note that left the cart, or pays less than it did, since
  the payment step was shown refuses the order before anything is written
  (`CheckoutCreditNotesGuard`, on `ORDER_PAY`). The cart is put right at that moment, so the next
  attempt goes through. Telling Thelia what the credit notes pay writes nothing.
  One limit comes from the order Thelia works in: it cancels the unpaid order the cart already
  had before it places the new one, so when the refusal falls on a cart that had such an order,
  that order is cancelled and its credit notes are given back. The customer keeps their cart
  and their credit notes, and confirms again. The refusal is an `InvalidPaymentException` of Thelia, which the Flexy
  theme shows on the payment step.
- **Ownership of the document.** The document of a credit note is served to its customer
  only (see Customer account), and from the back-office to the administrators who may view
  credit notes (see Rights).

## Tests

Two PHPUnit suites, configured by `phpunit.xml.dist`.

The **unit** suite needs no shop: the search expression builder, the wiring of the
numbering listener on the Propel events, the translation catalogs (every label the Twig
back-office and the PHP code translate exists in English and French, in its domain), and
the descriptors (module.xml, the loops of config.xml, the foreign keys and tables of
schema.xml against thelia.sql).

The **integration** suite runs through the Thelia kernel of a shop where the module is
active, inside a transaction rolled back after each test: activation and deactivation with
the seeded statuses, types and counters; the numbering of a credit note (reference on
insert, accounting number on acceptance, counters that never step back, even after a
deletion) and the status flow; the `credit-note`, `credit-note-detail` and
`credit-note-address` loops; the rows of the order and customer tabs; the balance of a credit
note; the account hook and the account PDF route (owner served, other customer, draft, guest and
visitor refused); the ceiling of an order; the use of a credit note, including a
second connection that waits on the row lock; the unique references; the PDF route of the
back-office (document served to an administrator as HTML and PDF, refused for a credit
note not accepted yet and for a visitor); the rights each back-office route takes; the
administration log; the amounts of the ceiling message in the language of the back-office;
the `credit-note:pdf:render` command; the payment of an order with credit notes through the
checkout of Thelia (order covered whole, rest left to a provider, several credit notes,
cancellation, a credit note to spend in a single purchase, another customer's credit note, a
credit note spent elsewhere before or while the order is placed); the payment step; the `order-credit-note` loop; the balance and the use in the back-office; the API,
its rights and the wall between two customers. The PDF route and command tests are skipped when the active
PDF theme has no `credit-note.html.twig` (thelia/pdf-default-template 1.2 or later).

From a shop the module is installed in:

```bash
# unit
THELIA_VENDOR_AUTOLOAD=$PWD/vendor/autoload.php php vendor/bin/phpunit -c vendor/thelia/modules/CreditNote/phpunit.xml.dist --testsuite unit

# integration, on the test database of the shop
APP_ENV=test THELIA_VENDOR_AUTOLOAD=$PWD/vendor/autoload.php php -d auto_prepend_file=./bootstrap.php vendor/bin/phpunit -c vendor/thelia/modules/CreditNote/phpunit.xml.dist --testsuite integration
```

`auto_prepend_file` loads the shop `bootstrap.php` before the PHPUnit entry script: the
Composer autoloader pulls in the core bootstrap, which otherwise derives the Thelia root
from its own location under `vendor/`. On PHP 8.5, add `-d display_errors=stderr`: the
deprecations some dependencies raise while the autoloader loads are otherwise written to
the output, which marks the headers as sent and keeps the native PHP session the web tests
clean up from starting.
