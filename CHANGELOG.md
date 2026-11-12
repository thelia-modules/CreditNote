# Unreleased

- Every back-office action on credit notes takes the right on the orders (`admin.order`) and on the module, both: VIEW to read the list, open a credit note, print its document or search a customer or an order in the modal; UPDATE to create or update one; DELETE to delete one. Creating, updating, deleting, the modal, the document and the two searches used to be open to any signed-in administrator, and the list asked for the modules resource (`admin.module`). **An administrator who manages credit notes now needs the right on the orders in their profile.** The configuration of the module still takes `admin.module`. The "Credit Notes" menu, the tab of the order page and the block of the customer page show only to an administrator who may view credit notes; the Add, Edit and Delete buttons follow the same rights.
- Creating, updating or deleting a credit note from the back-office writes a line in the administration log (`admin_log`), on the order of the credit note when it has one: its reference, its status, its total with tax, its order and its customer. A credit note the order ceiling refuses writes none.
- The credit notes of an order (tab "Credit note" of the order page) and of a customer are shown in the data table of default-twig, with the cells of the credit note list: reference, invoice reference with its date underneath, status, type, total with tax with the total without tax underneath, actions. The eleven columns of the previous table did not fit the tab at 1440 px. The Add button moved from the caption of the table to the header of the card. `CreditNoteHookPresenter::tableRows()` is removed, replaced by `CreditNoteListReader::ofOrder()` and `ofCustomer()` with `CreditNoteListRowPresenter`.
- New command `credit-note:pdf:render <id or reference> [--out file.pdf]`: writes the document of an accepted credit note to a file without the HTTP flow, as `pdf:render` of the core does for the invoice of an order. The document is the one the back-office serves, in the language of the credit note.
- The message that refuses a credit note beyond the total of its order writes its amounts with the separators of the language of the back-office ("1 408,80" in French) instead of "1,408.80" in every language.
- An empty comment in the modal no longer passes `null` to `trim()`, deprecated in PHP 8.5.
- A back-office route that names a credit note answers 404 when it does not exist. Updating an unknown id used to write a new credit note, opening it served the creation modal, and deleting it ended in an error 500.

# 4.4.0

- The credit note list of the back-office is laid out like the order list of default-twig: one search over the reference, the number, the order and the customer; status, type, date and amount filters in a collapsed panel; chips for the active filters; columns sorted by reference, total, creation or update date; pages of the `BoDataTable` and `BoPagination` components. The id column is gone; the reference opens the modal; the invoice reference of a numbered credit note has its column, with the date of its document underneath; the customer shows the flag of the invoice country, the name and the e-mail; a creation or update date shows its age underneath for 30 days, and only the date afterwards or for a date ahead of the clock. The search matches every word in any field, so the names of a customer can come in any order. The date filter bounds the invoice date, as before: the date of its creation for a credit note not numbered yet, the date of its number once accepted. A parameter of the wrong shape is ignored instead of answering 400. The previous parameters (`status_id` of the side menu, `ref`, `creditNoteDateMin`, `creditNoteDateMax`) still filter; `credit_note_page` and `credit_note_order` are no longer read (the list opens on its first page, newest first). Edit and Delete show only to an administrator with the update or delete right on the module. `CreditNoteHookPresenter::listRows()` is removed, replaced by `CreditNoteListReader` and `CreditNoteListRowPresenter`.

# 4.3.1

- The back-office screens are translated again: their labels used the `creditnote.bo.default` domain, which the kernel stopped loading once the Smarty back-office (`templates/backOffice/default/`) was removed, since it registers a `<module>.bo.<template>` domain only for a template the module ships. The domain is now `creditnote.bo.default-twig`, its catalogs moved to `I18n/backOffice/default-twig/`; the side menu reads "Avoirs" again in French. A shop that overrode one of these labels in `local/I18n` with a `creditnote.bo.default.` key renames it.

# 4.3.0

- A credit note has a language (`lang_id`), chosen in the back-office modal: the language of its order by default, of its customer without an order, the default language of the shop otherwise. It stays editable once the credit note is numbered, since it only changes the printed document. The document is printed in it (still within `document_locales`). The update fills the existing credit notes with the language they were printed in: the one of their customer, of their order when the customer has none.
- The colors of the status and type badges are written into `style` attributes only when they are hexadecimal colors, grey otherwise.
- A credit note created or saved without an invoice address (no customer picked, or "Other" with the name left empty) is refused with a message, and nothing is written. The controller used to save the empty address, which a database in strict mode refuses with an error 500 (`Field 'firstname' doesn't have a default value`). The module's back-office messages use the `danger` type, the one the Twig back office styles.
- `update()` runs the scripts of `Config/Update/sql/` from the version installed up to the target version only, compared by version (the file name without `.sql`). It used to run every script above the installed version, which replayed `4.3.0.sql` on a shop already at 4.3.0.
- After creating, updating or deleting a credit note, the administrator is back on the page the modal was opened from, on the tab that held it ("Credit note" of an order): the modal script sends that page as `success-url`, which the modal kept only from the query string. Only a path of the shop in printable ASCII is followed: an absolute or protocol-relative `success-url`, or one carrying a tab or a line break that the browser would drop, sends back to the order.
- The "Credit Notes" section of the admin side menu is back, right below "Orders": its script looked for the `#orders_menu` entry of the Smarty back-office, absent from default-twig. It now follows the entry `[data-testid="bo-nav-order"]` of the default-twig side menu, which has no hook between sections.

# 4.2.0

- Smarty is gone, as in Thelia 3: the Smarty plugin `credit_note_format_address`, the Smarty back-office and PDF templates, the Smarty fallback of the back-office hooks. The loops no Twig template calls are removed too: `credit-note-comment`, `credit-note-status`, `credit-note-type`, `order-credit-note` and `credit-note-version` (its table has not existed since 2.3.4). The loops `credit-note`, `credit-note-address` and `credit-note-detail` stay: the credit note document of the PDF template reads them.

# 4.1.0

- The languages a credit note is printed in are a setting of the module (`document_locales`, locales separated by commas): a customer whose language is in the list gets the document in that language, the others the default language of the shop. Empty, as before: English and French. A shop whose PDF template is translated in German adds `de_DE`. The field refuses anything that is not a list of locales such as `fr_FR`.

# 4.0.2

- The ceiling of an order is a setting of the module (`order_ceiling`, on by default, added to an existing installation by the update): a shop that grants credit notes beyond the total of the order turns it off.

# 4.0.1

- Credit notes numbered like the invoices (`invoice_ref_with_thelia_order`) draw their number from `InvoiceRefSequence::next()` (InvoiceRef 3.1 or later): the counter is read under a lock shared with the orders, and a number an order already carries is skipped. The previous read and increment, without a lock, could hand the same number to an invoice and a credit note.

# 4.0.0

- Thelia 3 port: back-office in Twig with a Smarty fallback, credit note PDF rendered from the PDF template (`credit-note.html.twig`, thelia-templates/pdf#16), order ceiling on credit notes.
- Requires Thelia 3.2. The 3.0.x line stays the Thelia 2.5 version.

# 2.3.0

- First public version, OpenSource <3
