<?php

declare(strict_types=1);

namespace CreditNote\Tests\Support;

/**
 * For a test that posts to the routes writing a credit note: the token the pages of the module
 * carry, read as the signed-in administrator gets it.
 */
trait ReadsCsrfToken
{
    /**
     * Read from the credit note list. Null when the list is closed to the administrator.
     */
    protected function csrfToken(): ?string
    {
        $this->client->request('GET', '/admin/credit-note');

        return 1 === preg_match('/name="_token" value="([^"]+)"/', (string) $this->client->getResponse()->getContent(), $found)
            ? html_entity_decode($found[1])
            : null;
    }
}
