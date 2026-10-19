# Unreleased

- A credit note has a language (`lang_id`), chosen in the back-office modal: the language of its order by default, of its customer without an order, the default language of the shop otherwise. It stays editable once the credit note is numbered, since it only changes the printed document. The document is printed in it (still within `document_locales`). The update fills the existing credit notes with the language they were printed in: the one of their customer, of their order when the customer has none.
- Smarty is gone, as in Thelia 3: the Smarty plugin `credit_note_format_address`, the Smarty back-office and PDF templates, the Smarty fallback of the back-office hooks. The loops no Twig template calls are removed too: `credit-note-comment`, `credit-note-status`, `credit-note-type`, `order-credit-note` and `credit-note-version` (its table has not existed since 2.3.4). The loops `credit-note`, `credit-note-address` and `credit-note-detail` stay: the credit note document of the PDF template reads them.
- The languages a credit note is printed in are a setting of the module (`document_locales`, locales separated by commas): a customer whose language is in the list gets the document in that language, the others the default language of the shop. Empty, as before: English and French. A shop whose PDF template is translated in German adds `de_DE`. The field refuses anything that is not a list of locales such as `fr_FR`.
- Credit notes numbered like the invoices (`invoice_ref_with_thelia_order`) draw their number from `InvoiceRefSequence::next()` (InvoiceRef 3.1 or later): the counter is read under a lock shared with the orders, and a number an order already carries is skipped. The previous read and increment, without a lock, could hand the same number to an invoice and a credit note.
- The ceiling of an order is a setting of the module (`order_ceiling`, on by default, added to an existing installation by the update): a shop that grants credit notes beyond the total of the order turns it off.

# 4.0.0

- Thelia 3 port: back-office in Twig with a Smarty fallback, credit note PDF rendered from the PDF template (`credit-note.html.twig`, thelia-templates/pdf#16), order ceiling on credit notes.
- Requires Thelia 3.2. The 3.0.x line stays the Thelia 2.5 version.

#2.3.0

- First public version, OpenSource <3
