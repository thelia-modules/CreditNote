<?php

declare(strict_types=1);

namespace CreditNote\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Every label the Twig back-office and the PHP code translate exists in the English and
 * French catalogs of its domain: the module domain (`creditnote`, I18n/), the back-office
 * domain (`creditnote.bo.default`, I18n/backOffice/default/) and the front-office domain
 * (`creditnote.fo.default`, I18n/frontOffice/default/).
 */
final class TranslationCatalogTest extends TestCase
{
    private const LOCALES = ['en_US', 'fr_FR'];

    private const CATALOGS = [
        'creditnote' => 'I18n/%s.php',
        'creditnote.bo.default' => 'I18n/backOffice/default/%s.php',
        'creditnote.fo.default' => 'I18n/frontOffice/default/%s.php',
    ];

    #[Test]
    #[DataProvider('catalogFiles')]
    public function theCatalogIsANonEmptyMapOfStringsWithoutDuplicateKey(string $file): void
    {
        $catalog = require self::root() . '/' . $file;

        self::assertIsArray($catalog);
        self::assertNotEmpty($catalog);

        foreach ($catalog as $id => $message) {
            self::assertIsString($id);
            self::assertIsString($message, \sprintf('[%s] the message of "%s" is a string', $file, $id));
            self::assertNotSame('', trim($message), \sprintf('[%s] the message of "%s" is not empty', $file, $id));
        }

        // A duplicate key in a PHP array literal is silently overridden: count the keys in
        // the source and compare with what PHP kept.
        $declared = preg_match_all("/^\s*(['\"])(?:(?!\\1).|\\\\.)*\\1\s*=>/m", (string) file_get_contents(self::root() . '/' . $file));
        self::assertSame($declared, \count($catalog), \sprintf('[%s] a key is declared twice', $file));
    }

    #[Test]
    #[DataProvider('domainsAndLocales')]
    public function theCatalogCoversEveryLabelOfItsDomain(string $domain, string $locale): void
    {
        $catalog = require self::root() . '/' . \sprintf(self::CATALOGS[$domain], $locale);
        $missing = array_values(array_filter(self::labelsUsed()[$domain] ?? [], static fn (string $id): bool => !isset($catalog[$id])));

        self::assertSame([], $missing, \sprintf('labels of the %s domain absent from the %s catalog', $domain, $locale));
    }

    #[Test]
    public function everyTranslationNamesADomainTheModuleShips(): void
    {
        $unknown = array_diff(array_keys(self::labelsUsed()), array_keys(self::CATALOGS));

        self::assertSame([], array_values($unknown), 'a label is translated in a domain without catalog (a trans() call without domain lands in the core domain and is never translated by the module)');
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function catalogFiles(): iterable
    {
        foreach (self::CATALOGS as $pattern) {
            foreach (self::LOCALES as $locale) {
                $file = \sprintf($pattern, $locale);

                yield $file => [$file];
            }
        }
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function domainsAndLocales(): iterable
    {
        foreach (array_keys(self::CATALOGS) as $domain) {
            foreach (self::LOCALES as $locale) {
                yield $domain . ' ' . $locale => [$domain, $locale];
            }
        }
    }

    /**
     * The labels the module translates, by domain: the `|trans` filters of the Twig
     * back-office templates and the `->trans()` calls of the PHP classes.
     *
     * @return array<string, list<string>>
     */
    private static function labelsUsed(): array
    {
        $labels = [];

        $twigFilter = "/(['\"])((?:(?!\\1).|\\\\.)*)\\1\s*\|\s*trans\(\s*(?:\{[^}]*\}|\[\])?\s*,?\s*(?:'([^']+)'|\"([^\"]+)\")?/";

        foreach (self::files(self::root() . '/templates/backOffice/default-twig', 'twig') as $file) {
            preg_match_all($twigFilter, (string) file_get_contents($file), $matches, \PREG_SET_ORDER);

            foreach ($matches as $match) {
                $labels[$match[3] !== '' ? $match[3] : ($match[4] ?? 'messages')][] = stripslashes($match[2]);
            }
        }

        $phpCall = "/->trans\(\s*(['\"])((?:(?!\\1).|\\\\.)*)\\1\s*(?:,\s*(?:\[[^\]]*\]|array\([^)]*\))\s*)?(?:,\s*([^,)]+))?/s";

        foreach (self::files(self::root(), 'php') as $file) {
            preg_match_all($phpCall, (string) file_get_contents($file), $matches, \PREG_SET_ORDER);

            foreach ($matches as $match) {
                $domain = trim($match[3] ?? '');
                $domain = str_contains($domain, 'DOMAIN_MESSAGE') ? 'creditnote' : trim($domain, "'\"");
                $labels[$domain === '' ? '(none)' : $domain][] = stripslashes($match[2]);
            }
        }

        foreach ($labels as $domain => $ids) {
            $labels[$domain] = array_values(array_unique($ids));
        }

        return $labels;
    }

    /**
     * @return list<string>
     */
    private static function files(string $directory, string $extension): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveCallbackFilterIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            static fn (\SplFileInfo $file): bool => !\in_array($file->getFilename(), ['vendor', 'tests', 'Model', 'I18n', 'Config', 'templates'], true)
                || str_contains($file->getPathname(), '/templates/backOffice/default-twig'),
        ));

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === $extension) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }

    private static function root(): string
    {
        return \dirname(__DIR__, 2);
    }
}
