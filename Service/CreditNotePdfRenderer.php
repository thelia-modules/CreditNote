<?php

declare(strict_types=1);

namespace CreditNote\Service;

use CreditNote\Model\CreditNote;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Core\Event\PdfEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Template\Parser\ParserResolver;
use Thelia\Core\Template\TemplateHelperInterface;

/**
 * Renders the printable credit note from the active PDF template, the way the core renders
 * the invoice: the parser is resolved against the PDF template (a document of the PDF theme
 * is never found through the back-office or front-office theme), then dompdf turns the HTML
 * into a PDF through the GENERATE_PDF event. Shared by the back-office and the customer
 * account routes.
 */
final readonly class CreditNotePdfRenderer
{
    public const DOCUMENT = 'credit-note';

    public function __construct(
        private ParserResolver $parserResolver,
        private TemplateHelperInterface $templateHelper,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function renderHtml(CreditNote $creditNote): string
    {
        $pdfTemplate = $this->templateHelper->getActivePdfTemplate();
        $parser = $this->parserResolver->getParser($pdfTemplate->getAbsolutePath(), self::DOCUMENT);
        $parser->setTemplateDefinition($pdfTemplate, true);

        return $parser->render(self::DOCUMENT, ['credit_note_id' => $creditNote->getId()]);
    }

    /**
     * The PDF bytes of the document, or null when no renderer answered.
     */
    public function renderPdf(CreditNote $creditNote): ?string
    {
        $pdfEvent = new PdfEvent($this->renderHtml($creditNote));
        $pdfEvent->setTemplateName(self::DOCUMENT);
        $pdfEvent->setFileName((string) $creditNote->getInvoiceRef());
        $pdfEvent->setObject($creditNote);

        $this->eventDispatcher->dispatch($pdfEvent, TheliaEvents::GENERATE_PDF);

        return $pdfEvent->hasPdf() ? $pdfEvent->getPdf() : null;
    }
}
