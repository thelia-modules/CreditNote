<?php

declare(strict_types=1);

namespace CreditNote\Command;

use CreditNote\Model\CreditNote;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Service\CreditNoteDocumentLocale;
use CreditNote\Service\CreditNotePdfRenderer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Thelia\Command\ContainerAwareCommand;
use Thelia\Model\LangQuery;

/**
 * Writes the document of a credit note to a file without the HTTP flow, to preview it or to
 * work on its template: what `pdf:render` of the core does for the invoice and the delivery
 * note of an order. The document is the one the back-office and the customer account serve,
 * in the language of the credit note.
 */
#[AsCommand(name: 'credit-note:pdf:render', description: 'Render the PDF of a credit note to a file without the HTTP flow, to preview it or work on its template')]
final class CreditNotePdfRenderCommand extends ContainerAwareCommand
{
    public function __construct(
        private readonly CreditNotePdfRenderer $pdfRenderer,
        private readonly CreditNoteDocumentLocale $documentLocale,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('credit-note', InputArgument::REQUIRED, 'Id or reference of the credit note, e.g. 12 or CN00000012')
            ->addOption('out', null, InputOption::VALUE_REQUIRED, 'Output file path (default: credit-note-<accounting number>.pdf)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $identifier = (string) $input->getArgument('credit-note');
        $creditNote = $this->find($identifier);

        if (null === $creditNote) {
            $output->writeln(\sprintf('<error>Credit note %s not found.</error>', $identifier));

            return Command::FAILURE;
        }

        // The same rule as the routes: a credit note has a document once it is accepted.
        if (true !== (bool) $creditNote->getCreditNoteStatus()?->getInvoiced()) {
            $output->writeln(\sprintf('<error>Credit note %s is not accepted yet: it has no document.</error>', $creditNote->getRef()));

            return Command::FAILURE;
        }

        // A PDF is generated outside an HTTP request: push a request with a session so the
        // parser and the formatting helpers have a locale and a currency to work with.
        $this->initRequest(LangQuery::create()->findOneByLocale($this->documentLocale->forCreditNote($creditNote)));

        try {
            $pdf = $this->pdfRenderer->renderPdf($creditNote);
        } catch (\Exception $exception) {
            $output->writeln(\sprintf('<error>%s</error>', $exception->getMessage()));

            return Command::FAILURE;
        }

        if (null === $pdf) {
            $output->writeln('<error>PDF generation produced no output.</error>');

            return Command::FAILURE;
        }

        $out = $input->getOption('out') ?? \sprintf('credit-note-%s.pdf', $creditNote->getInvoiceRef());

        if (false === @file_put_contents($out, $pdf)) {
            $output->writeln(\sprintf('<error>Could not write %s.</error>', $out));

            return Command::FAILURE;
        }

        $output->writeln(\sprintf('<info>Wrote %s (%d bytes).</info>', $out, \strlen($pdf)));

        return Command::SUCCESS;
    }

    private function find(string $identifier): ?CreditNote
    {
        if (ctype_digit($identifier)) {
            return CreditNoteQuery::create()->findPk((int) $identifier);
        }

        return CreditNoteQuery::create()->findOneByRef($identifier);
    }
}
