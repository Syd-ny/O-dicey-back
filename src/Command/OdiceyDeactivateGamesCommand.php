<?php

namespace App\Command;

use App\Entity\Game;
use DateInterval;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class OdiceyDeactivateGamesCommand extends Command
{
    protected static $defaultName = 'odicey:deactivate-games';
    protected static $defaultDescription = 'Deactive games with updates older than 1 year';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Use this command to browse through all games and deactivate the ones that have not been updated for a year.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Retrieve all games
        $games = $this->entityManager->getRepository(Game::class)->findAll();
        
        // Establish the period for which the game is playable/editable
        $date = new DateTimeImmutable();
        $delay = $date->sub(new DateInterval('P1Y'));

        foreach ($games as $game) {
            // If updatedAt is not null
            if(!is_null($game->getUpdatedAt())) {

                // If updatedAt is older than 1 year
                if($game->getUpdatedAt() < $delay) {

                    // Then the game's status is changed to inactive
                    $game->setStatus(2);
                }

            } 
  
        }

        $this->entityManager->flush();

        $io->success('Done! All games that have not been updated for a year are now inactive. They can no longer be edited or deleted by the DM.');

        return Command::SUCCESS;
    }
}