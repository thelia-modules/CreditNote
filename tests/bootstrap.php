<?php

declare(strict_types=1);

// The module classes lean on the core (Symfony components, Thelia models): the autoloader
// of the shop the module is installed in provides them, or the module's own vendor after a
// composer install at its root (unit suite only). THELIA_VENDOR_AUTOLOAD points at another
// vendor/autoload.php. A checkout linked into a shop by symlink resolves __DIR__ to its real
// path, so it falls through to its own vendor: set THELIA_VENDOR_AUTOLOAD to the shop's for
// the integration suite.
$candidates = array_filter([
    getenv('THELIA_VENDOR_AUTOLOAD') ?: null,
    // local/modules/CreditNote
    __DIR__ . '/../../../../vendor/autoload.php',
    // vendor/thelia/modules/CreditNote
    __DIR__ . '/../../../../../vendor/autoload.php',
    // composer install at the module root
    dirname(__DIR__) . '/vendor/autoload.php',
]);

$autoload = null;

foreach ($candidates as $candidate) {
    if (is_file($candidate)) {
        $autoload = $candidate;
        break;
    }
}

if ($autoload === null) {
    fwrite(\STDERR, "No vendor/autoload.php found: install the module in a shop, run composer install at the module root, or set THELIA_VENDOR_AUTOLOAD.\n");
    exit(1);
}

// The core bootstrap (autoloaded with the vendor) derives THELIA_ROOT from its own
// location, which is vendor/thelia/ in a shop installed by Composer: the shop defines
// the constants in its root bootstrap.php before loading the vendor, so do the same.
$shopRoot = dirname($autoload, 2);
$shopBootstrap = $shopRoot . '/bootstrap.php';

if (is_file($shopBootstrap)) {
    require $shopBootstrap;
}

require $autoload;

// Reached only when something loaded the autoloader before this file: the PHPUnit entry
// script does, before it reads any configuration, so the require above came too late and
// the path constants are already the ones the core derived from vendor/. Nothing can undo
// a define(), and the kernel would boot looking for the core schema under
// vendor/thelia/vendor/thelia/config/: say what to run instead.
$vendorDirectory = realpath(dirname($autoload)) . DIRECTORY_SEPARATOR;

if (is_file($shopBootstrap) && defined('THELIA_ROOT') && str_starts_with((string) realpath(THELIA_ROOT), $vendorDirectory)) {
    fwrite(\STDERR, sprintf(
        "THELIA_ROOT is %s, inside the vendor directory, so the path constants all point below it.\n"
        . "The Composer autoloader was loaded before the shop bootstrap.php. Prepend it to the PHPUnit entry script:\n"
        . "  php -d auto_prepend_file=%s vendor/bin/phpunit -c <module>/phpunit.xml.dist --testsuite integration\n",
        THELIA_ROOT,
        $shopBootstrap,
    ));
    exit(1);
}

// The shop kernel reads its parameters from the environment (DEFAULT_URI, the database):
// load the shop .env the way the shop test bootstrap does. In test mode Dotenv skips
// .env.local by design, where the database access of a development shop usually lives:
// bridge the DATABASE_* variables from it when nothing else defined them.
$shopEnvFile = dirname($autoload, 2) . '/.env';

if (is_file($shopEnvFile) && class_exists(\Symfony\Component\Dotenv\Dotenv::class)) {
    (new \Symfony\Component\Dotenv\Dotenv())->bootEnv($shopEnvFile);

    $shopEnvLocalFile = $shopEnvFile . '.local';

    if (empty($_SERVER['DATABASE_HOST']) && is_file($shopEnvLocalFile)) {
        $localVariables = (new \Symfony\Component\Dotenv\Dotenv())->parse((string) file_get_contents($shopEnvLocalFile));

        foreach (['DATABASE_HOST', 'DATABASE_PORT', 'DATABASE_NAME', 'DATABASE_USER', 'DATABASE_PASSWORD'] as $key) {
            if (isset($localVariables[$key]) && empty($_SERVER[$key])) {
                $_SERVER[$key] = $_ENV[$key] = $localVariables[$key];
            }
        }
    }
}

// The module and its tests, when the shop autoloader does not map them already
// (module checked out outside local/modules). Longest prefix first.
spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'CreditNote\\Tests\\' => __DIR__ . '/',
        'CreditNote\\' => dirname(__DIR__) . '/',
    ];

    foreach ($prefixes as $prefix => $directory) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $file = $directory . str_replace('\\', '/', substr($class, \strlen($prefix))) . '.php';

        if (is_file($file)) {
            require $file;
        }

        return;
    }
});
