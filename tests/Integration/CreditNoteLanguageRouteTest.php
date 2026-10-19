<?php

declare(strict_types=1);

namespace CreditNote\Tests\Integration;

use CreditNote\CreditNote;
use CreditNote\Model\CreditNote as CreditNoteModel;
use CreditNote\Model\CreditNoteQuery;
use CreditNote\Tests\Support\AdminSessionInjector;
use CreditNote\Tests\Support\CreditNoteFixture;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Thelia\Model\Lang;
use Thelia\Model\LangQuery;
use Thelia\Model\ModuleQuery;
use Thelia\Test\FixtureFactory;
use Thelia\Test\WebIntegrationTestCase;

/**
 * The language of a credit note is chosen in the back-office modal and stays editable once the
 * credit note is numbered: it only changes the printed document, never the number nor the
 * amounts. A language the credit note cannot have is ignored.
 */
final class CreditNoteLanguageRouteTest extends WebIntegrationTestCase
{
    private CreditNoteFixture $fixture;

    private AdminSessionInjector $injector;

    protected function setUp(): void
    {
        parent::setUp();

        if (!ModuleQuery::create()->findOneByCode(CreditNote::getModuleCode())?->getActivate()) {
            self::markTestSkipped('The CreditNote module is not active in the test database.');
        }

        $this->fixture = new CreditNoteFixture($this->getPropelConnection());
        $this->injector = new AdminSessionInjector();
        $this->getService(EventDispatcherInterface::class)->addSubscriber($this->injector);
    }

    protected function tearDown(): void
    {
        if (isset($this->injector)) {
            $this->injector->clear();
        }

        parent::tearDown();
    }

    #[Test]
    public function theLanguageOfANumberedCreditNoteCanBeChanged(): void
    {
        $creditNote = $this->numberedCreditNote();
        $german = $this->activeLang('de_DE', 'de', 'Deutsch');
        $this->loginAdmin();

        $this->update($creditNote, $german->getId());

        $saved = CreditNoteQuery::create()->findPk($creditNote->getId());
        self::assertSame($german->getId(), $saved->getLangId());
        self::assertSame($creditNote->getRef(), $saved->getRef());
        self::assertSame($creditNote->getInvoiceRef(), $saved->getInvoiceRef());
        self::assertSame($creditNote->getTotalPriceWithTax(), $saved->getTotalPriceWithTax());
        self::assertSame(1, $saved->getCreditNoteDetails()->count());
    }

    #[Test]
    public function aLanguageTheCreditNoteCannotHaveIsIgnored(): void
    {
        $creditNote = $this->numberedCreditNote();
        $before = $creditNote->getLangId();
        self::assertNotNull($before, 'the credit note got a language on insert');

        // Inside the test transaction: rolled back with the rest.
        $inactive = LangQuery::create()->findOneByLocale('it_IT')
            ?? (new FixtureFactory($this->getPropelConnection()))->lang(['locale' => 'it_IT', 'code' => 'it', 'title' => 'Italiano']);
        $inactive->setActive(false)->setByDefault(0)->save($this->getPropelConnection());
        $this->loginAdmin();

        $this->update($creditNote, $inactive->getId());
        self::assertSame($before, CreditNoteQuery::create()->findPk($creditNote->getId())->getLangId());

        $this->update($creditNote, 999999);
        self::assertSame($before, CreditNoteQuery::create()->findPk($creditNote->getId())->getLangId());
    }

    private function numberedCreditNote(): CreditNoteModel
    {
        $customer = $this->fixture->customer();
        $creditNote = $this->fixture->creditNote($customer, $this->fixture->paidOrder($customer), 'accepted');
        $creditNote->save();
        self::assertNotNull($creditNote->getInvoiceRef(), 'an accepted credit note is numbered');

        return $creditNote;
    }

    private function update(CreditNoteModel $creditNote, int $langId): void
    {
        $this->client->request('POST', \sprintf('/admin/credit-note/%d/_update', $creditNote->getId()), [
            'credit_note_create' => [
                'action' => 'update',
                'lang_id' => $langId,
                'ui_target' => 'order',
                'ui_target_id' => $creditNote->getOrderId(),
            ],
        ]);

        self::assertTrue($this->client->getResponse()->isRedirection(), (string) $this->client->getResponse()->getContent());
    }

    private function activeLang(string $locale, string $code, string $title): Lang
    {
        // Inside the test transaction: rolled back with the rest.
        $lang = LangQuery::create()->findOneByLocale($locale)
            ?? (new FixtureFactory($this->getPropelConnection()))->lang(['locale' => $locale, 'code' => $code, 'title' => $title]);
        $lang->setActive(true)->save($this->getPropelConnection());

        return $lang;
    }

    private function loginAdmin(): void
    {
        // Built directly rather than through createFixtureFactory(): that helper pushes a
        // synthetic request on the stack, which would become the main request the security
        // context resolves the session from for every request the client sends afterwards.
        $admin = (new FixtureFactory($this->getPropelConnection()))->admin();
        $admin->eraseCredentials();
        $this->injector->setAdmin($admin);
    }
}
