<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Mode;
use App\Entity\Game;
use App\Entity\Gallery;
use App\Entity\Character;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\OdiceyProvider;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

class AppFixtures extends Fixture
{

    private $connection;

    /**
    * Constructor
    */
    public function __construct( Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Truncate tables and reset autoincrementation 
     */
    private function truncate()
    {
        // Deactivate foreign key checks
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // Truncate
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
        // Truncate
        $this->truncate();

        // Faker instanciation
        $faker = Factory::create();

        // Mode Fixture
        // create a new Mode object (always the same for V1 of our app)
        $mode = new Mode();
        $mode->setName('Dungeons and Dragons 5');

        // ! possibly have to change the syntax of the json stats
        $mode->setJsonStats(
            [
                'hp: integer,
                race: string,
                class: string,
                background: string,
                level: integer,
                characteristics: {
                    strength: 10,
                    dexterity: 10,
                    constitution: 10,
                    wisdom: 10,
                    intelligence: 10,
                    charisma: 10 }',
                'skills: {
                    acrobatics: 10,
                    animal_handling: 10,
                    arcana: 10,
                    athletics: 10,
                    deception: 10,
                    history: 10,
                    insight: 10,
                    intimidation: 10,
                    investigation: 10,
                    medicine: 10,
                    nature: 10,
                    perception: 10,
                    performance: 10,
                    persuasion: 10,
                    religion: 10,
                    sleight_of_hand: 10,
                    stealth: 10,
                    survival: 10 }',
                'armor_class: integer,
                initiative: integer,
                speed: integer,
                passive_wisdom: integer',
                'attacks: [
                    {
                        weapon: nom,
                        damage: 1d8 perforants,
                        properties: finesse, légère, à deux mains, etc
                    },
                    {
                        weapon: nom,
                        damage: 1d8 perforants,
                        properties: finesse, légère, à deux mains, etc
                    }
                ]',
                'spells: [
                    {
                        spell: nom,
                        level: integer,
                        school: abjuration, conjuration, 
                                  divination, enchantment, evocation, 
                                  illusion, necromancy, transmutation,
                        casting_time: 1 action,
                        range: 30 feet,
                        components: V, S, M,
                        duration: instantanée,
                        effects: description ici,
                        at_higher_levels: description ici
                    },
                    {
                        spell: nom,
                        level: integer,
                        school: abjuration, conjuration, 
                                  divination, enchantment, evocation, 
                                  illusion, necromancy, transmutation,
                        casting_time: 1 action,
                        range: 30 feet,
                        components: V, S, M,
                        duration: instantanée,
                        effects: description ici,
                        at_higher_levels: description ici
                    }
                ]'   
            ]
        );

        // Game Fixtures
        for ($g=0; $g < 10; $g++) {
            // create a new Game object
            $game = new Game();
            // assign values to properties
            $game->setName($faker->sentence(3));
            $game->setStatus($faker->numberBetween(0, 1));
            $game->setCreatedAt(new DateTimeImmutable($faker->date()));
            $game->setMode($mode);

            // Gallery Fixtures
            for ($i=0; $i < mt_rand(1, 10); $i++) {
                // create a new Gallery object
                $gallery = new Gallery();
                // assign values to properties
                $gallery->setPicture("https://picsum.photos/id/".mt_rand(1,180)."/300/500");
                $gallery->setMainPicture(0);
                $gallery->setGame($game);
    
                // persist
                $manager->persist($gallery);
            }

            // persist
            $manager->persist($game);
        }

        $manager->flush();
    }
}
