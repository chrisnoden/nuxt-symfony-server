<?php

namespace App\Twig\Runtime;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use Twig\Extension\RuntimeExtensionInterface;

class MarkdownMarkupRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function markdownToHtml(string $content): string
    {
        // Configure the Environment with all the CommonMark parsers/renderers
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());

        // Add this extension
        $environment->addExtension(new TableExtension());

        // Instantiate the converter engine and start converting some Markdown!
        $converter = new MarkdownConverter($environment);

        return $converter->convert($content);
    }
}
