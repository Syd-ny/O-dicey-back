<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Mode;
use App\Entity\Game;
use App\Entity\Gallery;
use App\Entity\Character;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\OflixProvider;
use App\Service\MySlugger;
use Doctrine\DBAL\Connection;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private $slugger;
    private $connection;

    /**
    * Constructor
    */
    public function __construct(MySlugger $slugger, Connection $connection)
    {
        $this->slugger = $slugger;
        $this->connection = $connection;
    }

    /**
     * Permet de TRUNCATE les tables et de remettre les AI à 1
     */
    private function truncate()
    {
        // On passe en mode SQL ! On cause avec MySQL
        // Désactivation la vérification des contraintes FK
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // On tronque
        $this->connection->executeQuery('TRUNCATE TABLE user');
        $this->connection->executeQuery('TRUNCATE TABLE mode');
        $this->connection->executeQuery('TRUNCATE TABLE game');
        $this->connection->executeQuery('TRUNCATE TABLE game_users');
        $this->connection->executeQuery('TRUNCATE TABLE gallery');
        $this->connection->executeQuery('TRUNCATE TABLE character');
        // etc.
    }

    public function load(ObjectManager $manager): void
    {
        // tronquage des tables
        $this->truncate();

        // instanciation faker
        $faker = Factory::create();

        // 

        $manager->flush();
    }
}
