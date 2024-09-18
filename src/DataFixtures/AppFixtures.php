<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
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

        $this->loadManyUsers($manager);
    }

    private function loadManyUsers(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $client2 = ($manager->getRepository(Client::class))->find(2);

        // create 30 users
        for ($i = 0; $i < 30; $i++) {
            $user = (new User())
                ->setName($faker->name())
                ->setEmail($faker->email())
                ->setClient($client2)
                ->setEnabled($faker->boolean())
                ->setPassword($faker->sha256())
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
