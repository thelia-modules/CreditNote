# Credit Note

Author: Gilles Bourgeat <gilles.bourgeat@gmail.com>

* Allows granting credit notes to customers

## Compatibility

Thelia >= 2.3

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is ```CreditNote```.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

For Thelia < 2.5
```
composer require thelia/credit-note-module:~2.3.0
```
For Thelia >= 2.5
```
composer require thelia/credit-note-module:~3.0.1
```

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
domain (`I18n/frontOffice/default/`).

## Guarantees

- **Ceiling of an order.** The credit notes of an order never refund more than the order was
  worth: when a credit note is written (whatever writes it: back-office, API, a command), the
  total with tax of the other non-refused credit notes of the order plus its own must stay
  within the total of the order, postage and discount included (`CreditNoteOrderCeiling`,
  enforced by the numbering listener before any number is drawn). The back-office shows the
  refusal as an error message on the order page.
- **Unique numbering.** The reference and the accounting number come from counters that only
  move forward and are never reused, even after a deletion; the database refuses a duplicate
  of either (`ref_UNIQUE`, `invoice_ref_UNIQUE`).
- **Atomic use.** `CreditNoteConsumption::consume()` writes the use of a credit note on an
  order inside a transaction that locks the credit note row (`SELECT … FOR UPDATE`) and reads
  the balance again under the lock: two orders paid at the same time with the same credit
  note consume its balance once, the second one waits and is refused when nothing is left.
  It also refuses a credit note that is not accepted, already used, owned by another
  customer, already used on that order, a partial use of a credit note that forbids it, and
  marks the credit note used when its balance reaches zero. The checkout does not call it
  yet: it is the primitive the cart integration will build on.
- **Ownership of the document.** The document of a credit note is served to its customer
  only (see Customer account), and to administrators from the back-office.

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
note not accepted yet and for a visitor). The PDF route test is skipped when the active
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
