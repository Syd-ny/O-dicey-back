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
use Doctrine\DBAL\Connection;
use App\Entity\GameUsers;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    private $connection;

    /**
    * Constructor
    */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    private function truncate()
    {
        $this->connection->executeQuery('SET foreign_key_checks = 0');
        // Truncate
        $this->connection->executeQuery('TRUNCATE TABLE `character`');// backticks are used to avoid an SQL error about 'character' being a reserved term.
        $this->connection->executeQuery('TRUNCATE TABLE gallery');
        $this->connection->executeQuery('TRUNCATE TABLE game');
        $this->connection->executeQuery('TRUNCATE TABLE game_users');
        $this->connection->executeQuery('TRUNCATE TABLE mode');
        $this->connection->executeQuery('TRUNCATE TABLE user');
        
    }

    public function load(ObjectManager $manager): void
    {

        // Truncate tables
        $this->truncate();

        // Faker instance
        $faker = Factory::create();
        // Include Provider
        $faker->addProvider(new OdiceyProvider());

        // Mode Fixtures
        // Create a new Mode object (always the same for V1 of our app)
        $mode = new Mode();
        $mode->setName('Dungeons and Dragons 5');

        $mode->setJsonstats([
            'hp' => 'integer',
            'race' => 'string',
            'class' => 'string',
            'background' => 'string',
            'level' => 'integer',
            'characteristics' => [
                'strength' => 10,
                'dexterity' => 10,
                'constitution' => 10,
                'wisdom' => 10,
                'intelligence' => 10,
                'charisma' => 10
            ],
            'skills' => [
                'acrobatics' => 10,
                'animal_handling' => 10,
                'arcana' => 10,
                'athletics' => 10,
                'deception' => 10,
                'history' => 10,
                'insight' => 10,
                'intimidation' => 10,
                'investigation' => 10,
                'medicine' => 10,
                'nature' => 10,
                'perception' => 10,
                'performance' => 10,
                'persuasion' => 10,
                'religion' => 10,
                'sleight_of_hand' => 10,
                'stealth' => 10,
                'survival' => 10
            ],
            'armor_class' => 'integer',
            'initiative' => 'integer',
            'speed' => 'integer',
            'passive_wisdom' => 'integer',
            'attacks' => [
                [
                    'weapon' => 'nom',
                    'damage' => '1d8 perforants',
                    'properties' => 'finesse, légère, à deux mains, etc'
                ],
                [
                    'weapon' => 'nom',
                    'damage' => '1d8 perforants',
                    'properties' => 'finesse, légère, à deux mains, etc'
                ]
            ],
            'spells' => [
                [
                    'spell' => 'nom',
                    'level' => 'integer',
                    'school' => 'abjuration, conjuration, divination, enchantment, evocation, illusion, necromancy, transmutation',
                    'casting_time' => '1 action',
                    'range' => '30 feet',
                    'components' => 'V, S, M',
                    'duration' => 'instantanée',
                    'effects' => 'description ici',
                    'at_higher_levels' => 'description ici'
                ],
                [
                    'spell' => 'nom',
                    'level' => 'integer',
                    'school' => 'abjuration, conjuration, divination, enchantment, evocation, illusion, necromancy, transmutation',
                    'casting_time' => '1 action',
                    'range' => '30 feet',
                    'components' => 'V, S, M',
                    'duration' => 'instantanée',
                    'effects' => 'description ici',
                    'at_higher_levels' => 'description ici'
                ]
            ]
        ]);

        // Persist the mode
        $manager->persist($mode);
        
        // User Fixtures
        $users = [];
        for ($i=0; $i < 20; $i++) { 
            
            // Create my User object
            $user = new User();
            // Set its parameters
            $user->setEmail($faker->unique()->safeEmail());
            $user->setLogin($faker->unique()->name());
            $user->setPassword(password_hash($user->getLogin(),PASSWORD_DEFAULT));
            $user->setPicture("https://picsum.photos/id/".mt_rand(1,180)."/300/500");
            $user->setCreatedAt(new DateTimeImmutable($faker->date()));
            $user->setRoles(['ROLE_USER']);
            
            // Add the user to the array
            $users[] = $user;
            
            // Persist
            $manager->persist($user);
        }

        // Admin Fixture
        $admin = $users[0];
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($admin);
        
        // Game Fixtures
        $games = [];
        for ($g=0; $g < 10; $g++) {
            // Create a new Game object
            $game = new Game();
            // Assign values to properties
            $game->setName($faker->sentence(3));
            $game->setStatus($faker->numberBetween(0, 1));
            $game->setCreatedAt(new DateTimeImmutable($faker->date()));
            $game->setMode($mode);
            
            // Choose a DM for the game
            $game->setDm($users[$faker->numberBetween(0,count($users) -1)]);
            
            // Gallery Fixtures
            for ($i=0; $i < mt_rand(1, 10); $i++) {
                // Create a new Gallery object
                $gallery = new Gallery();
                // Assign values to properties
                $gallery->setPicture("https://picsum.photos/id/".mt_rand(1,180)."/300/500");
                $gallery->setMainPicture(0);
                $gallery->setGame($game);
                
                // Persist
                $manager->persist($gallery);
            }
            
            // Add the game to the $games array
            $games[] = $game;

            // Persist
            $manager->persist($game);
        }

        // Character Fixtures
        for ($i = 0; $i < 10; $i++) {
            // 1. create entity
            $newCharacter = new Character;
            $race = mt_rand(1,4);
            // 2. properties to update
            if($race === 1) {
                $newCharacter->setName($faker->unique()->dwarves()); 
            }

            elseif($race === 2) {
                $newCharacter->setName($faker->unique()->humans());
            }

            elseif($race === 3) {
                $newCharacter->setName($faker->unique()->elves());
            }
            
            else{
                $newCharacter->setName($faker->unique()->orcs());
            };
            
            $newCharacter->setPicture($faker->imageUrl(450, 300, true));
            $newCharacter->setUser($users[mt_rand(1, count($users) - 1)]);
            $newCharacter->setGame($games[mt_rand(1, count($games) - 1)]);
            $newCharacter->setCreatedAt(new DateTimeImmutable($faker->date()));

            // 3. give to doctrine
            $manager->persist($newCharacter);
        }

        // Game-Users Fixtures
        for ($i = 0; $i < 10; $i++) {
            // Create a new Invitation 
            $invitation = new GameUsers();

            // Associate user_id and game_id
            $invitation->setUser($users[mt_rand(1, count($users) - 1)]);
            $invitation->setGame($games[mt_rand(1, count($games) - 1)]);
            // Set status
            $invitation->setStatus(1);
            // Persist
            $manager->persist($invitation);
        }

        $manager->flush();
    }
}
