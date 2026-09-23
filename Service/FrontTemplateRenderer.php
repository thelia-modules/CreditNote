<?php

declare(strict_types=1);

namespace CreditNote\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Thelia\Core\Template\TemplateHelperInterface;
use Twig\Environment;

/**
 * Renders a front-office fragment of the module under the active theme. The theme overrides a
 * fragment by shipping templates/frontOffice/<theme>/modules/CreditNote/<name>; the module file
 * under templates/frontOffice/default/CreditNote/ is the default.
 */
final readonly class FrontTemplateRenderer
{
    private const MODULE_TEMPLATE_DIRECTORY = '@CreditNoteModule/frontOffice/default/CreditNote/';

    private const THEME_OVERRIDE_DIRECTORY = 'frontOffice/%s/modules/CreditNote/';

    public function __construct(
        private Environment $twig,
        // No interface alias in the core container: the helper is only known by its service id.
        #[Autowire(service: 'thelia.template_helper')]
        private TemplateHelperInterface $templateHelper,
    ) {
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function render(string $templateName, array $variables): string
    {
        return $this->twig->render($this->resolveTemplate($templateName), $variables);
    }

    private function resolveTemplate(string $templateName): string
    {
        $themeOverride = \sprintf(self::THEME_OVERRIDE_DIRECTORY, $this->templateHelper->getActiveFrontTemplate()->getName()) . $templateName;

        return $this->twig->getLoader()->exists($themeOverride)
            ? $themeOverride
            : self::MODULE_TEMPLATE_DIRECTORY . $templateName;
    }
}
