<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(
    path: '/',
    name: 'default_',
)]
class DocumentationController extends SymfonyController
{

    #[Route(
        path: '',
        name: 'index',
    )]
    public function defaultController(): Response
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            return new RedirectResponse('/docs/', Response::HTTP_TEMPORARY_REDIRECT);
        }

        return new Response('service working as at: ' . date('Y-m-d H:i:s'));
    }

    #[Route(
        path: '/docs/{slug}',
        name: 'documentation',
        requirements: ['slug' => '[a-z0-9A-Z-_\/\.]*']
    )]
    public function documentationController(string $slug): Response
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            $finder = new Finder();
            $finder->files()
                   ->name('*.md')
                   ->sortByName()
                   ->in(dirname(__DIR__, 2) . '/docs/')
            ;

            $f = '01_index.md';

            if ($slug !== '') {
                $f = $slug;
                if (substr($f, -3) !== '.md') {
                    $f .= '.md';
                }
            }

            $files = [];

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                if (str_contains($file->getRelativePath(), '_')) {
                    list ($idx, $path) = explode('_', $file->getRelativePath(), 2);
                } else {
                    $idx = '00';
                    $path = '';
                }

                if (!isset($files[(int)$idx])) {
                    $files[(int)$idx] = [
                        'name'  => $path,
                        'files' => [],
                    ];
                }

                $name = preg_replace('/^[0-9]+/', '', $file->getFilenameWithoutExtension());
                $name = ucwords(
                    trim(preg_replace('/[^0-9a-zA-Z]+/', ' ', $name))
                );
                $name = preg_replace('/(2fa)/', '2FA', $name);

                array_push($files[(int)$idx]['files'], [
                    'name'             => $name,
                    'relativePathname' => $file->getRelativePathname(),
                    'filename'         => $file->getFilename(),
                ]);
            }

            ksort($files);

            return $this->render('docs/markdown-doc.html.twig', [
                'files'   => $files,
                'content' => file_get_contents(dirname(__DIR__, 2) . '/docs/' . $f)
            ]);
        }

        return new Response('only available in dev environment', Response::HTTP_FORBIDDEN);
    }
}
