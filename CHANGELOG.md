# Unreleased

- Credit notes numbered like the invoices (`invoice_ref_with_thelia_order`) draw their number from `InvoiceRefSequence::next()` (InvoiceRef 3.1 or later): the counter is read under a lock shared with the orders, and a number an order already carries is skipped. The previous read and increment, without a lock, could hand the same number to an invoice and a credit note.

# 4.0.0

- Thelia 3 port: back-office in Twig with a Smarty fallback, credit note PDF rendered from the PDF template (`credit-note.html.twig`, thelia-templates/pdf#16), order ceiling on credit notes.
- Requires Thelia 3.2. The 3.0.x line stays the Thelia 2.5 version.

#2.3.0

- First public version, OpenSource <3