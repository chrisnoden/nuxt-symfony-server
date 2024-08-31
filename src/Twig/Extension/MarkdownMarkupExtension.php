<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\MarkdownMarkupRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MarkdownMarkupExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'markdown_markup',
                [MarkdownMarkupRuntime::class, 'markdownToHtml'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
