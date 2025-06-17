<?php

namespace Atournayre\Bundle\MakerBundle\Template;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Service for rendering templates for code generation.
 */
class TemplateRenderer
{
    private Environment $twig;
    
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * Render a template with the given parameters.
     *
     * @param string $template The template name
     * @param array $parameters The parameters to pass to the template
     * @return string The rendered template
     * @throws LoaderError When the template cannot be found
     * @throws RuntimeError When an error occurred during rendering
     * @throws SyntaxError When an error occurred during compilation
     */
    public function render(string $template, array $parameters = []): string
    {
        return $this->twig->render($template, $parameters);
    }
    
    /**
     * Generate a file from a template.
     *
     * @param string $targetPath The path where the file should be created
     * @param string $template The template name
     * @param array $parameters The parameters to pass to the template
     * @throws LoaderError When the template cannot be found
     * @throws RuntimeError When an error occurred during rendering
     * @throws SyntaxError When an error occurred during compilation
     */
    public function generateFile(string $targetPath, string $template, array $parameters = []): void
    {
        $content = $this->render($template, $parameters);
        
        $directory = dirname($targetPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        
        file_put_contents($targetPath, $content);
    }
}
