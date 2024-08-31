<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Load any required data for dev/testing
 */
class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /** @var Connection $conn */
        $conn = $manager->getConnection();

        $finder = new Finder();
        $finder->sortByName();
        $finder->files()
            ->in(dirname(__FILE__, 3) . '/tests/Support/Data/');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            printf('     > %s%s', $file->getFilename(), PHP_EOL);

            $conn->executeStatement(file_get_contents($file->getRealPath()));
        }
    }
}
