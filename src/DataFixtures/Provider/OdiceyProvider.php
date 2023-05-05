<?php

namespace App\DataFixtures\Provider;

class OdiceyProvider
{
    // Array of games
    private $games = [
        
    ];

    // array of users
    private $users = [
        
    ];

    // array of galleries
    private $galleries = [
        
    ];

    // array of characters
    private $characters = [
        
    ];

    /**
     * Get a random game
     */
    public function game()
    {
        return $this->games[array_rand($this->games)];
    }

    /**
     * Get a random user
     */
    public function user()
    {
        return $this->users[array_rand($this->users)];
    }

     /**
     * Get a random gallery
     */
    public function gallery()
    {
        return $this->galleries[array_rand($this->galleries)];
    }

    /**
     * Get a random character
     */
    public function character()
    {
        return $this->characters[array_rand($this->characters)];
    }
}