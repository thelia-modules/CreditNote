<?php

declare(strict_types=1);

namespace CreditNote\Controller\Front;

use CreditNote\Model\CreditNoteQuery;
use CreditNote\Service\CreditNotePdfRenderer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Thelia\Controller\Front\BaseFrontController;
use Thelia\Model\Customer;

/**
 * The printable credit note, served to its customer from their account. The document carries
 * the address and the purchases of a customer: it is served on the ownership of the signed-in
 * customer, never on its id alone, and only once the merchant accepted the credit note.
 */
final class CreditNoteAccountController extends BaseFrontController
{
    #[Route('/account/credit-note/{id}/pdf', name: 'credit_note_account_pdf', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function pdf(int $id, CreditNotePdfRenderer $pdfRenderer): Response
    {
        $this->checkAuth();

        $customer = $this->getSecurityContext()->getCustomerUser();
        $creditNote = CreditNoteQuery::create()->findPk($id);

        // One answer whatever the reason: a credit note of someone else does not exist here.
        if (
            !$customer instanceof Customer
            || null === $creditNote
            || (int) $creditNote->getCustomerId() !== (int) $customer->getId()
            || !$creditNote->getCreditNoteStatus()->getInvoiced()
        ) {
            throw new NotFoundHttpException();
        }

        $pdf = $pdfRenderer->renderPdf($creditNote);

        if (null === $pdf) {
            throw new NotFoundHttpException();
        }

        return $this->pdfResponse($pdf, (string) $creditNote->getInvoiceRef(), 200, true);
    }
}
