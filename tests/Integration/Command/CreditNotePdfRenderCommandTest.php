<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration\Command;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Thelia\Core\Template\TemplateHelperInterface;
use Thelia\Model\ModuleQuery;
use Thelia\Test\IntegrationTestCase;

/**
 * credit-note:pdf:render writes the document of an accepted credit note to a file, found by
 * its id or by its reference, and refuses a credit note that has no document yet.
 */
final class CreditNotePdfRenderCommandTest extends IntegrationTestCase
{
    private CreditNoteFixture $fixture;

    private string $out;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $pdfTemplate = $this->getService(TemplateHelperInterface::class)->getActivePdfTemplate();

        if (!is_file($pdfTemplate->getAbsolutePath().'/credit-note.html.twig')) {
            self::markTestSkipped(\sprintf('The active PDF template (%s) ships no credit note document.', $pdfTemplate->getName()));
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
        $this->out = sys_get_temp_dir().'/credit-note-render-'.bin2hex(random_bytes(4)).'.pdf';
    }

    protected function tearDown(): void
    {
        if (isset($this->out) && is_file($this->out)) {
            unlink($this->out);
        }

        parent::tearDown();
    }

    #[Test]
    public function itWritesTheDocumentOfAnAcceptedCreditNoteFoundByItsId(): void
    {
        $creditNote = $this->creditNote('accepted');
        $tester = $this->tester();

        $exitCode = $tester->execute(['credit-note' => (string) $creditNote->getId(), '--out' => $this->out]);

        self::assertSame(Command::SUCCESS, $exitCode, $tester->getDisplay());
        self::assertFileExists($this->out);
        self::assertStringStartsWith('%PDF', (string) file_get_contents($this->out));
    }

    #[Test]
    public function itFindsTheCreditNoteByItsReference(): void
    {
        $creditNote = $this->creditNote('accepted');
        $tester = $this->tester();

        $exitCode = $tester->execute(['credit-note' => $creditNote->getRef(), '--out' => $this->out]);

        self::assertSame(Command::SUCCESS, $exitCode, $tester->getDisplay());
        self::assertStringStartsWith('%PDF', (string) file_get_contents($this->out));
    }

    #[Test]
    public function itRefusesACreditNoteThatIsNotAcceptedYet(): void
    {
        $creditNote = $this->creditNote('proposed');
        $tester = $this->tester();

        $exitCode = $tester->execute(['credit-note' => (string) $creditNote->getId(), '--out' => $this->out]);

        self::assertSame(Command::FAILURE, $exitCode);
        self::assertStringContainsString('not accepted yet', $tester->getDisplay());
        self::assertFileDoesNotExist($this->out);
    }

    #[Test]
    public function itFailsOnAnUnknownCreditNote(): void
    {
        $tester = $this->tester();

        self::assertSame(Command::FAILURE, $tester->execute(['credit-note' => '0', '--out' => $this->out]));
        self::assertStringContainsString('not found', $tester->getDisplay());

        self::assertSame(Command::FAILURE, $tester->execute(['credit-note' => 'CN-UNKNOWN', '--out' => $this->out]));
        self::assertFileDoesNotExist($this->out);
    }

    private function creditNote(string $statusCode): CreditNoteModel
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), $statusCode);
        $creditNote->save();

        return $creditNote;
    }

    private function tester(): CommandTester
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        return new CommandTester($application->find('credit-note:pdf:render'));
    }
}
